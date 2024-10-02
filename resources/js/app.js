import CartModal from "./CartModal";
import ReservationList from "./ReservationList";
window.$ = window.jQuery = require("jquery");
const cartModal = new CartModal();
const reservationList = new ReservationList();
require("./UserForm");
require("./bootstrap");
