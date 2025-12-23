            </main>
        </div>
    </div>

    <script>
        // Sidebar toggle for mobile
        $('#sidebarToggle').on('click', function() {
            $('#sidebar').toggleClass('hidden');
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
    </script>
</body>
</html>
