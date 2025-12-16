import $ from "jquery";
window.$ = window.jQuery = $;

import "./bootstrap";
import Alpine from "alpinejs";
window.Alpine = Alpine;
Alpine.start();

// DataTables default
import "datatables.net-dt/css/dataTables.dataTables.css";
import DataTable from "datatables.net-dt";

// SweetAlert2
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
window.Swal = Swal;

// Helper toast
window.showToast = function (type, message) {
    Swal.fire({
        toast: true,
        position: "top-end",
        icon: type,
        title: message,
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
    });
};

document.addEventListener("DOMContentLoaded", function () {
    const flashMessage = document.getElementById("flash-message");
    if (flashMessage) {
        const type = flashMessage.getAttribute("data-type");
        const message = flashMessage.getAttribute("data-message");
        window.showToast(type, message);
    }
});

// Inisialisasi DataTables
$(document).ready(function () {
    $("#adminsTable").DataTable({
        responsive: true,
        pageLength: 10,
        lengthChange: false,
        language: {
            searchPlaceholder: "Cari...",
            search: "",
        },
    });

    $("#teachersTable").DataTable({
        responsive: true,
        pageLength: 10,
        lengthChange: false,
        language: {
            searchPlaceholder: "Cari...",
            search: "",
        },
    });
    $("#studentsTable").DataTable({
        responsive: true,
        pageLength: 10,
        lengthChange: false,
        language: {
            searchPlaceholder: "Cari...",
            search: "",
        },
    });
    $("#classroomsTable").DataTable({
        responsive: true,
        pageLength: 10,
        lengthChange: false,
        language: {
            searchPlaceholder: "Cari...",
            search: "",
        },
    });
    $("#subjectsTable").DataTable({
        responsive: true,
        pageLength: 10,
        lengthChange: false,
        language: {
            searchPlaceholder: "Cari...",
            search: "",
        },
    });
    $("#assignmentsTable").DataTable({
        responsive: true,
        pageLength: 10,
        lengthChange: false,
        language: {
            searchPlaceholder: "Cari...",
            search: "",
        },
    });
    $("#assessmentsTable").DataTable({
        responsive: true,
        pageLength: 10,
        lengthChange: false,
        language: {
            searchPlaceholder: "Cari...",
            search: "",
        },
    });
});
