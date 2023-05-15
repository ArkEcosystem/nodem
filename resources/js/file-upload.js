const FileUpload = ($uploadID, $model = null) => ({
    model: $model,
    isUploading: false,
    isErrored: false,
    uploadEl: null,

    init() {
        this.uploadEl = document.getElementById($uploadID);

        window.addEventListener("validation-error", (event) => {
            this.isErrored = true;
        });

        window.addEventListener("reset-wizard", (event) => {
            this.model = null;
            this.isUploading = false;
            this.isErrored = false;
        });
    },

    select() {
        let element = this.uploadEl;

        if (element === null) {
            document.getElementById($uploadID).click();
        } else {
            this.uploadEl.click();
        }
    },
});

export default FileUpload;
