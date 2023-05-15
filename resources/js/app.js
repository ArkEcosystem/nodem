import Alpine from "alpinejs";
import "@ui/js/tippy.js";
import { Chart, registerables } from "chart.js";
import annotationPlugin from "chartjs-plugin-annotation";
import Dropdown from "./dropdown";
import Modal from "@ui/js/modal";
import Navbar from "@ui/js/navbar";
import Pagination from "@ui/js/pagination";
import Pikaday from "pikaday";
import ProgressBar from "progressbar.js";
import RichSelect from "@ui/js/rich-select.js";
import Slider from "@ui/js/slider.js";
import { picasso } from "@vechain/picasso";
import "./vendor/ark/reposition-dropdown";
import FileUpload from "./file-upload.js";
import "@ui/js/tabs.js";
import "focus-visible";

// Note: package sets width and height of the svg to 100, which we don't need
window.createAvatar = (seed) =>
    picasso(seed).replace('width="100" height="100"', "");

window.Chart = Chart;
Chart.register(...registerables);
Chart.register(annotationPlugin);

window.Alpine = Alpine;
window.Dropdown = Dropdown;
window.Modal = Modal;
window.Navbar = Navbar;
window.Pagination = Pagination;
window.Pikaday = Pikaday;
window.ProgressBar = ProgressBar;
window.RichSelect = RichSelect;
window.Slider = Slider;
window.FileUpload = FileUpload;

Alpine.start();
