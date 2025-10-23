function copyCode(event) {
    event.preventDefault(); // Prevent potential form submission
    const codeElement = document.getElementById('copyTarget');
    const codeText = codeElement.textContent || codeElement.innerText; // Get the text content
    const copyIcon = document.getElementById('copyButtonIcon');
    const successIcon = document.getElementById('successIcon');
    const successMessage = document.getElementById('successMessage');

    function showSuccess() {
        copyIcon.classList.add('hidden');
        successIcon.classList.remove('hidden');
        successMessage.classList.remove('hidden');

        setTimeout(() => {
            successMessage.classList.add('hidden');
            successIcon.classList.add('hidden');
            copyIcon.classList.remove('hidden');
        }, 2500); // Revert after 2.5 seconds
    }

    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(codeText).then(() => {
            showSuccess(); // Add a visual cue that it was copied
        }).catch(err => {
            //
        });
    } else {
        const range = document.createRange();
        range.selectNode(codeElement);
        window.getSelection().removeAllRanges();
        window.getSelection().addRange(range);
        try {
            document.execCommand('copy');
            showSuccess();
        } catch (err) {
            //
        }
        window.getSelection().removeAllRanges();
    }
}

window.copyCode = copyCode;
