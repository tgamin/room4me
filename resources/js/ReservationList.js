export default class ReservationList {
    constructor() {
        this.init();
    }

    init() {
        $(document).ready(function () {
            $(".reservation-list td:first-child").on("click", function (e) {
                e.preventDefault();
                $(".reservation-list td:not(:first-child)").addClass("hidden");
                $(this).parent().find("td").removeClass('hidden');
                $(".reservation-list .accordion-icon").removeClass("rotate-180");
                $(this).find(".accordion-icon").addClass("rotate-180");
            });
        });
    }
};