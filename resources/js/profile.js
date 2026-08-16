document.getElementById("avatar_url").addEventListener("change", function (e) {
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function (event) {
        document.getElementById("avatar-preview").src = event.target.result;
        document
            .getElementById("avatar-preview-wrap")
            .classList.remove("hidden");
        document.getElementById("avatar-icon").classList.add("hidden");
    };
    reader.readAsDataURL(file);
});
