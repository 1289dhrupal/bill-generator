                </div>
            </main>
        </div>
    </div>
    <script src="./assets/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/feather.min.js"></script>
    <script src="./assets/js/scripts.js"></script>
    <?php if ($title == 'Dashboard') { ?>
        <script>
            $(document).on('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && (e.key == "p" || e.charCode == 16 || e.charCode == 112 || e.keyCode == 80)) {
                    let printWindow = window.open(
                        '<?php echo str_replace('dashboard.php', 'print.php', "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"); ?>',
                        'Print',
                        'left=200, top=200, width=950, height=500, toolbar=0, resizable=,'
                    )
                    printWindow.addEventListener(
                        'load',
                        () => {
                            printWindow.print()
                            // printWindow.close()
                        },
                        true
                    )

                    e.cancelBubble = true;
                    e.preventDefault();
                    e.stopImmediatePropagation();
                }
            });

            function printInNewWindow() {}
        </script>
    <?php } ?>

</body>

</html>
<?php
