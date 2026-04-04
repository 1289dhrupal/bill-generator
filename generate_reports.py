# pip install pandas jinja2 playwright
# playwright install chromium
import os
import shutil
import re
import pandas as pd
from jinja2 import Template
from playwright.sync_api import sync_playwright

# ==========================================
# CONFIGURATION
# ==========================================
CSV_FILE_PATH = 'input/data.csv'
OUTPUT_DIR = 'output'
SPLIT_BILLS_DIR = os.path.join(OUTPUT_DIR, 'split_bills')

# Playwright PDF settings to mimic Chrome "75% scale, Normal Margins"
PDF_MARGINS = {"top": "0.5in", "right": "0.5in",
               "bottom": "0.5in", "left": "0.5in"}
PDF_SCALE = 0.75

# ==========================================
# HTML TEMPLATES (Mimicking PHP UI)
# ==========================================
CSS_STYLES = """
<style>
    body { font-family: Arial, sans-serif; font-size: 13px; color: #333; }
    h2 { margin: 0; color: #2c3e50; font-size: 24px; }
    .bill-header { display: flex; justify-content: space-between; margin-bottom: 20px; }
    .bill-header .address { text-align: right; line-height: 1.4; }
    .pagebreak { page-break-after: always; }
    tr, .summary-box { page-break-inside: avoid; break-inside: avoid; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 12px; }
    th, td { border: 2px solid #dee2e6; padding: 8px; text-align: center; }
    thead th, tfoot th { background-color: #f8f9fa; font-weight: bold; color: #000; }
    .table-light { background-color: #fbfbfb; }
    .summary-box { width: 300px; float: right; border: 2px solid #6c757d; margin-top: 20px; }
    .summary-header { background-color: #f8f9fa; padding: 8px 15px; border-bottom: 2px solid #6c757d; }
    .summary-body { padding: 15px; }
    .summary-body div { display: flex; justify-content: space-between; margin-bottom: 5px; }
    .clear { clear: both; }
    .text-start { text-align: left; }
    .text-end { text-align: right; }
    hr { border-top: 2px solid #ccc; margin: 10px 0; }
</style>
"""

DETAILED_TEMPLATE = CSS_STYLES + """
{% for party, records in grouped_data.items() %}
<div class="bill-container">
    <div class="bill-header">
        <div>
            <h2>{{ party }}</h2>
        </div>
        <div class="address">
            <strong>Shah Paresh Shakralal (Cento)</strong><br>
            B-6/203, Veena Nagar CHS 2<br>
            LBS Marg, Mulund (W) Mumbai -80<br>
            Contact - 9322231596, 8291711596<br>
            PAN - ASPPS2654H
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>SR.</th><th>DATE</th><th>BILL</th><th>ITEM</th><th>MAKE</th>
                <th>QTY</th><th>RATE</th><th>TOTAL</th><th>PARTY</th><th>EX RATE</th><th>EX TOTAL</th>
            </tr>
        </thead>
        <tbody class="table-light">
            {% for row in records %}
            <tr>
                <td>{{ loop.index }}</td>
                <td>{{ row['DATE'] }}</td>
                <td>{{ row['BILL'] }}</td>
                <td class="text-start">{{ row['ITEM'] }}</td>
                <td>{{ row['MAKE'] }}</td>
                <td>{{ row['QTY'] }}</td>
                <td class="text-end">{{ row['RATE'] }}</td>
                <td class="text-end">{{ row['TOTAL'] }}</td>
                <td class="text-start">{{ row['PARTY'] }}</td>
                <td class="text-end">{{ row['EX RATE'] }}</td>
                <td class="text-end">{{ row['EX TOTAL'] }}</td>
            </tr>
            {% endfor %}
        </tbody>
    </table>
    
    <div class="summary-box">
        <div class="summary-header">
            <strong>SUMMARY</strong>
        </div>
        <div class="summary-body">
            <div><strong>BROKERAGE AT 1%:</strong> <span>{{ summaries[party]['brokerage'] }}</span></div>
            <div><strong>EXTRA:</strong> <span>{{ summaries[party]['extra'] }}</span></div>
            <hr>
            <div><strong>TOTAL:</strong> <strong>{{ summaries[party]['grand_total'] }}</strong></div>
        </div>
    </div>
    <div class="clear"></div>
    <div class="pagebreak"></div>
</div>
{% endfor %}
"""

SUMMARY_TEMPLATE = CSS_STYLES + """
<h2 style="text-align: center; margin-bottom: 20px;">Summary</h2>
<table>
    <thead>
        <tr>
            <th>SR</th>
            <th class="text-start">PARTY NAME</th>
            <th class="text-end">TOTAL</th>
            <th class="text-end">EX TOTAL</th>
            <th class="text-end">BROKERAGE</th>
        </tr>
    </thead>
    <tbody class="table-light">
        {% for row in summary_data %}
        <tr>
            <th>{{ loop.index }}</th>
            <td class="text-start">{{ row['party'] }}</td>
            <td class="text-end">{{ row['total'] }}</td>
            <td class="text-end">{{ row['ex_total'] }}</td>
            <td class="text-end">{{ row['brokerage'] }}</td>
        </tr>
        {% endfor %}
    </tbody>
    <tfoot>
        <tr>
            <th>SR</th>
            <th class="text-start">PARTY NAME</th>
            <th class="text-end">TOTAL</th>
            <th class="text-end">EX TOTAL</th>
            <th class="text-end">BROKERAGE</th>
        </tr>
    </tfoot>
</table>
"""

# ==========================================
# DATA PROCESSING & GENERATION
# ==========================================


def clean_date(d):
    """ Mimics PHP: if (strlen($date[2]) !== 4) $current_row['DATE'] = "{$date[0]}-{$date[1]}-20{$date[2]}"; """
    if not isinstance(d, str):
        return d
    parts = str(d).strip().split('-')
    if len(parts) == 3 and len(parts[2]) == 2:
        return f"{parts[0]}-{parts[1]}-20{parts[2]}"
    return d


def sanitize_filename(name):
    """ Replaces anything that is not alphanumeric with a dash, and collapse multiple dashes """
    safe_name = re.sub(r'[^a-zA-Z0-9]', '-', name)
    safe_name = re.sub(r'-+', '-', safe_name).strip('-')
    return safe_name


def process_and_generate():
    print("Reading and cleaning CSV...")

    if not os.path.exists(CSV_FILE_PATH):
        print(f"Error: Could not find {CSV_FILE_PATH}")
        return

    # Read all columns as strings to prevent LossySetitemError on empty fills
    df = pd.read_csv(CSV_FILE_PATH, dtype=str)
    df = df.fillna('')

    # Make sure we have a PARTY NAME column
    if 'PARTY NAME' not in df.columns:
        if 'PARTY' in df.columns:
            df['PARTY NAME'] = df['PARTY']
        elif 'COMPANY' in df.columns:
            df['PARTY NAME'] = df['COMPANY']
        else:
            print(
                "Error: Could not find 'PARTY NAME' or equivalent grouping column in CSV.")
            return

    # Clean dates
    if 'DATE' in df.columns:
        df['DATE'] = df['DATE'].apply(clean_date)

    # Format numeric columns safely
    numeric_cols = ['RATE', 'TOTAL', 'EX RATE', 'EX TOTAL']
    for col in numeric_cols:
        if col in df.columns:
            df[col] = pd.to_numeric(df[col].astype(str).str.replace(
                ',', ''), errors='coerce').fillna(0)

    grouped_data = {}
    summaries = {}
    summary_data = []

    # Process each party exactly like process_data.php & generate_tables.php
    for party in df['PARTY NAME'].unique():
        if not str(party).strip():
            continue

        party_df = df[df['PARTY NAME'] == party]

        sum_total = party_df['TOTAL'].sum()
        sum_ex_total = party_df['EX TOTAL'].sum()

        # Detailed logic:
        # $brokerage = round(floatval($total_sum) / 100, 2);
        brokerage_at_1 = sum_total / 100.0
        extra = sum_ex_total
        detailed_grand_total = brokerage_at_1 + extra

        # Summary logic:
        # $data[$party_name]['BROKERAGE'] += (($entry['TOTAL'] / 100) + $entry['EX TOTAL']);
        # This resolves to exactly the same as detailed_grand_total

        summaries[party] = {
            'brokerage': f"{brokerage_at_1:,.2f}",
            'extra': f"{extra:,.2f}",
            'grand_total': f"{detailed_grand_total:,.2f}"
        }

        summary_data.append({
            'party': party,
            'total': f"{sum_total:,.2f}",
            'ex_total': f"{sum_ex_total:,.2f}",
            'brokerage': f"{detailed_grand_total:,.2f}"
        })

        # Format rows for display
        formatted_records = []
        for _, row in party_df.iterrows():
            formatted_row = row.to_dict()
            for col in numeric_cols:
                if col in formatted_row:
                    # Omit .00 if needed, or enforce 2 decimals:
                    formatted_row[col] = f"{float(formatted_row[col]):.2f}"
            formatted_records.append(formatted_row)

        grouped_data[party] = formatted_records

    # Setup directories
    if os.path.exists(OUTPUT_DIR):
        shutil.rmtree(OUTPUT_DIR)
    os.makedirs(SPLIT_BILLS_DIR)

    # Initialize Playwright and generate PDFs
    with sync_playwright() as p:
        print("Launching Chromium browser for PDF generation...")
        browser = p.chromium.launch()
        page = browser.new_page()

        # 1. Generate summary.pdf
        print("Generating summary.pdf...")
        tmpl_summary = Template(SUMMARY_TEMPLATE)
        html_summary = tmpl_summary.render(summary_data=summary_data)

        page.set_content(html_summary)
        page.pdf(
            path=os.path.join(OUTPUT_DIR, 'summary.pdf'),
            format="A4",
            margin=PDF_MARGINS,
            scale=PDF_SCALE
        )

        # 2. Generate bills.pdf (Detailed view for all)
        print("Generating bills.pdf...")
        tmpl_detailed = Template(DETAILED_TEMPLATE)
        html_all_bills = tmpl_detailed.render(
            grouped_data=grouped_data, summaries=summaries)

        page.set_content(html_all_bills)
        page.pdf(
            path=os.path.join(OUTPUT_DIR, 'bills.pdf'),
            format="A4",
            margin=PDF_MARGINS,
            scale=PDF_SCALE
        )

        # 3. Generate Split Bills
        print("Generating individual split bills...")
        for index, (party, records) in enumerate(grouped_data.items(), start=1):
            single_party_data = {party: records}
            html_single = tmpl_detailed.render(
                grouped_data=single_party_data, summaries=summaries)

            safe_party_name = sanitize_filename(str(party))
            filename = f"{index}-{safe_party_name}.pdf"
            filepath = os.path.join(SPLIT_BILLS_DIR, filename)

            page.set_content(html_single)
            page.pdf(
                path=filepath,
                format="A4",
                margin=PDF_MARGINS,
                scale=PDF_SCALE
            )

        browser.close()

    # 4. Zip the split bills directory
    print("Zipping bills...")
    zip_path = os.path.join(OUTPUT_DIR, 'bills')
    shutil.make_archive(zip_path, 'zip', SPLIT_BILLS_DIR)

    # Cleanup the unzipped folder
    shutil.rmtree(SPLIT_BILLS_DIR)

    print(f"Success! Files generated in the '{OUTPUT_DIR}' directory.")


if __name__ == "__main__":
    process_and_generate()
