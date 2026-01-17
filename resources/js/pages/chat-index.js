document.addEventListener('DOMContentLoaded', function () {
    const userSearch = document.getElementById('userSearch');
    const userItems = document.querySelectorAll('.user-item');
    const noResults = document.getElementById('noResults');

    if (userSearch) {
        userSearch.addEventListener('input', function () {
            const query = this.value.toLowerCase().trim();
            let hasVisible = false;

            userItems.forEach(item => {
                const name = item.getAttribute('data-name');
                const email = item.getAttribute('data-email');

                if (name.includes(query) || email.includes(query)) {
                    item.classList.remove('d-none');
                    item.classList.add('d-flex');
                    hasVisible = true;
                } else {
                    item.classList.remove('d-flex');
                    item.classList.add('d-none');
                }
            });

            if (noResults) {
                if (hasVisible) {
                    noResults.classList.add('d-none');
                } else {
                    noResults.classList.remove('d-none');
                }
            }
        });
    }

    // Scroll chatBox (if in show view)
    const chatBox = document.getElementById('chatBox');
    if (chatBox) {
        chatBox.scrollTop = chatBox.scrollHeight;
    }
});
