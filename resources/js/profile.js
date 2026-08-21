const avatarInput = document.getElementById("avatar_url");
const avatarPreview = document.getElementById("avatar-preview");
const avatarPreviewWrap = document.getElementById("avatar-preview-wrap");
const avatarIcon = document.getElementById("avatar-icon");

if (avatarInput && avatarPreview && avatarPreviewWrap && avatarIcon) {
    avatarInput.addEventListener("change", function (e) {
        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function (event) {
            avatarPreview.src = event.target.result;
            avatarPreviewWrap.classList.remove("hidden");
            avatarIcon.classList.add("hidden");
        };
        reader.readAsDataURL(file);
    });
}
