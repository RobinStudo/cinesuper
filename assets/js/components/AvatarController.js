class AvatarController {
    constructor() {
        this.avatarController = document.getElementById("avatarContainer");
        this.formContainer = document.getElementById("addPictureFormContainer");
        this.header = document.getElementsByTagName("header");
        this.main = document.getElementById("restContent");
        this.footer = document.getElementsByTagName("footer");
        this.closeButton = document.getElementById("closeForm");
    }

    displayForm() {
        this.formContainer.classList.toggle("invisible");
        this.header[0].classList.toggle("filter");
        this.footer[0].classList.toggle("filter");
        this.main.classList.toggle("filter");
    }

    initControls() {
        this.avatarController.addEventListener("click", this.displayForm.bind(this));
        this.closeButton.addEventListener("click", (e) => {
            e.preventDefault();
            this.displayForm();
        });
    }
}

let avatarController = new AvatarController();
avatarController.initControls();
