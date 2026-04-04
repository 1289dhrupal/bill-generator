# pip install PyPDF2

import shutil
import PyPDF2
import re
import os


def split_pdf_robust(input_pdf_path, output_dir="split_pdfs"):
    # Create the output directory if it doesn't exist
    if not os.path.exists(output_dir):
        os.makedirs(output_dir)

    with open(input_pdf_path, 'rb') as file:
        reader = PyPDF2.PdfReader(file)

        current_writer = PyPDF2.PdfWriter()
        current_company = None
        file_index = 1

        for i in range(len(reader.pages)):
            page = reader.pages[i]
            text = page.extract_text()

            if not text:
                current_writer.add_page(page)
                continue

            lines = [line.strip() for line in text.split('\n') if line.strip()]
            if not lines:
                current_writer.add_page(page)
                continue

            new_company_name = None

            # Check the first few lines for the "Shah Paresh" header
            for line_idx in range(min(3, len(lines))):
                line_text = lines[line_idx]

                # Look for "Shah Paresh" or "Shah P aresh" ignoring case
                if re.search(r'Shah\s*P\s*aresh|Shah\s*Paresh', line_text, re.IGNORECASE):
                    if line_idx == 0:
                        # If it is on the very first line, it got merged.
                        # Split the string and take everything before "Shah"
                        split_parts = re.split(
                            r'Shah\s*P\s*aresh|Shah\s*Paresh', line_text, flags=re.IGNORECASE)
                        if split_parts[0].strip():
                            new_company_name = split_parts[0].strip()
                    else:
                        # If it is on line 2 or 3, the company name is the line right before it
                        new_company_name = lines[line_idx - 1].strip()
                    break

            # If we found a new company, save the previous batch and start a new one
            if new_company_name:
                if current_company and len(current_writer.pages) > 0:
                    safe_name = re.sub(r'[\\\\/*?:"<>|]',
                                       "", current_company).strip()
                    output_filename = os.path.join(
                        output_dir, f"{file_index}-{safe_name}.pdf")

                    with open(output_filename, 'wb') as out_file:
                        current_writer.write(out_file)
                        print(f"Saved: {output_filename}")

                    file_index += 1

                current_writer = PyPDF2.PdfWriter()
                current_company = new_company_name

            # Add the current page to the active writer
            current_writer.add_page(page)

        # Save the very last generated PDF chunk when the loop finishes
        if current_company and len(current_writer.pages) > 0:
            safe_name = re.sub(r'[\\\\/*?:"<>|\.]', "",
                               current_company).strip()
            output_filename = os.path.join(
                output_dir, f"{file_index}-{safe_name}.pdf")

            with open(output_filename, 'wb') as out_file:
                current_writer.write(out_file)
                print(f"Saved: {output_filename}")


def zip_output_folder(folder_path, output_zip_name):
    """
    Compresses the specified folder into a zip file.
    """
    if os.path.exists(folder_path):
        # make_archive automatically adds the .zip extension to the output_zip_name
        shutil.make_archive(output_zip_name, 'zip', folder_path)
        print(f"\nSuccess: Zipped all files into {output_zip_name}.zip")
    else:
        print(f"\nError: The folder '{folder_path}' does not exist.")


if __name__ == "__main__":
    pdf_filename = "Detailed List.pdf"
    output_directory = "split_pdfs"
    zip_filename = "Split_Company_Invoices"

    print("Starting PDF split process...")
    # Call your split function
    split_pdf_robust(pdf_filename, output_dir=output_directory)

    print("PDF split complete. Zipping the folder...")
    # Call the new zip function
    zip_output_folder(output_directory, zip_filename)
