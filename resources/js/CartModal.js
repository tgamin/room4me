import { Modal } from "flowbite";
export default class CartModal {
    constructor() {
        if (document.getElementById("modalEl")) {
            this.init();
            this.setButtonsListeners();
            this.onCheckingTimeChange();
            this.onCheckoutTimeChange();
        }
    }

    hideModal(reset = false) {
        this.modal.hide();

        if (reset) {
            if (this.serviceId == "505") {
                this.checkingTimeSelect.value = "";
                this.checkingTimeHidden.value = "";
            } else if (this.serviceId == "508") {
                this.checkoutTimeSelect.value = "";
                this.checkoutTimeHidden.value = "";
            }
        }
    }

    onCheckingTimeChange() {
        this.checkingTimeSelect.addEventListener("change", () => {
            this.setService("505");
            this.checkingTimeHidden.value = this.checkingTimeSelect.value;
            if (
                this.checkingTimeSelect.value && this.checkingTimeSelect.value != '00:00'
                && (this.checkingTimeSelect.value <
                this.checkingTimeSelectDefaultValue)
            ) {
                this.showModal();
            }
        });
    }

    onCheckoutTimeChange() {
        this.checkoutTimeSelect.addEventListener("change", () => {
            this.setService("508");
            this.checkoutTimeHidden.value = this.checkoutTimeSelect.value;
            if (
                this.checkoutTimeSelect.value &&
                (this.checkoutTimeSelect.value >
                    this.checkoutTimeSelectDefaultValue)
            ) {
                this.showModal();
            }
        });
    }

    showModal() {
        const clone = this.details.cloneNode(true);
        const modalBody = document.getElementById("modal-body");
        modalBody.innerHTML = "";
        modalBody.appendChild(clone);
        const cloneServiceForm =
            clone.getElementsByClassName("service-form")[0];
        if (cloneServiceForm) {
            cloneServiceForm.remove();
        }
        this.modal.show();
    }

    setService(serviceId) {
        this.serviceId = serviceId;
        this.form = document.getElementById(`service-form-${this.serviceId}`);
        this.checkingTimeHidden = document.getElementById(`checking_time_hidden-${this.serviceId}`);
        this.checkoutTimeHidden = document.getElementById(`checkout_time_hidden-${this.serviceId}`);
        this.details = document.getElementById(
            `service-details-${this.serviceId}`
        );
    }

    init() {
        this.modalId = "modalEl";
        const $targetEl = document.getElementById(this.modalId);
        this.modal = new Modal($targetEl, {
            backdrop: "dynamic",
            backdropClasses:
                "bg-gray-900 bg-opacity-50 dark:bg-opacity-80 fixed inset-0 z-40",
            closable: true,
        });
        this.checkingTimeSelect = document.getElementById("checking_time");
        this.checkoutTimeSelect = document.getElementById("checkout_time");
        this.checkingTimeSelectDefaultValue = "15:00";
        this.checkoutTimeSelectDefaultValue = "11:00";
    }

    setButtonsListeners() {
        document
            .getElementById("close-modal-button")
            .addEventListener("click", () => {
                this.hideModal(true);
            });
        document
            .getElementById("accept-modal-button")
            .addEventListener("click", () => {
                this.form.submit();
                this.hideModal();
            });
        document
            .getElementById("decline-modal-button")
            .addEventListener("click", () => {
                this.hideModal(true);
            });
    }
};