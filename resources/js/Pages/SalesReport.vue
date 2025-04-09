<script setup>
import { ref, onMounted } from "vue";

onMounted(() => {
    updateDateTime();
    setInterval(updateDateTime, 1000);
});

const date = ref("");
const time = ref("");
const period = ref("");

const updateDateTime = () => {
    const now = new Date();
    let hours = now.getHours();
    const minutes = now.getMinutes();
    period.value = hours >= 12 ? "PM" : "AM";

    hours = hours % 12 || 12;
    time.value = `${String(hours).padStart(2, "0")}:${String(minutes).padStart(2, "0")}`;
    const options = { weekday: "short", day: "2-digit", month: "long", year: "numeric" };
    date.value = now.toLocaleDateString("en-US", options);
};
</script>

<style>
@keyframes shimmer {
    0% {
        background-position: 200%;
    }
    100% {
        background-position: -200%;
    }
}

.shimmer {
    background: linear-gradient(90deg, rgb(51 65 85) 0%, rgb(130, 145, 165) 50%, rgb(51 65 85) 100%);
    background-size: 200% auto;
    background-clip: text;
    -webkit-background-clip: text;
    color: transparent;
    -webkit-text-fill-color: transparent;
    animation: shimmer 4s infinite linear;
}
</style>
<template>
    <div class="flex flex-row h-screen w-full bg-[#F8F8F8] tracking-tight overflow-y-hidden no-select">
        <div class="flex flex-col h-screen w-full px-4 gap-3">
            <div class="flex flex-row w-full pt-4 h-auto items-center justify-between">
                <div class="flex flex-row w-auto">
                    <div class="back-to-pos w-[3.2rem] h-[3.2rem] flex items-center justify-center rounded-full bg-white text-[#2D71F8] cursor-pointer">
                        <i class="ri-arrow-go-back-fill text-current text-xl"></i>
                    </div>
                    <div class="text-lg font-medium text-slate-700 flex items-center pl-5 justify-center">Sales Report
                    </div>
                </div>
                <div class="flex flex-row w-auto gap-4">
                    <div class="jam w-36 h-[3.2rem] rounded-full items-center bg-white flex">
                        <div class="wrap px-2 flex flex-row items-center justify-between w-full">
                            <p class="text-slate-700 font-semibold ml-3">{{ time }} <span class="text-slate-400">{{ period }}</span></p>
                            <div class="icon w-9 h-9 bg-[#f0f7ff] rounded-full flex items-center justify-center text-[#2D71F8]">
                                <i class="ri-time-line text-current text-lg"></i>
                            </div>
                        </div>
                    </div>
                    <div class="tanggal w-60 h-[3.2rem] rounded-full  bg-white flex">
                        <div class="flex flex-row items-center justify-between w-full pl-5 pr-2">
                            <p class="text-slate-700 font-medium">{{ date }}</p>
                            <div class="icon w-9 h-9 bg-[#f0f7ff] rounded-full flex items-center justify-center text-[#2D71F8]">
                                <i class="ri-calendar-line text-current text-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex flex-row justify-between w-full h-14 rounded-2xl bg-white">
                <div class="flex items-center justify-center gap-3">
                    <div class="text-gray-400 text-sm font-medium pl-2 py-9">Date Periode:</div>
                    <div class="flex items-center justify-between h-[70%] w-24 rounded-full border border-slate-300 bg-transparent">
                        <div class="text-sm font-medium text-slate-700 pl-3">Today</div>
                        <div class="icon w-7 h-7 bg-[#f0f7ff] rounded-full flex items-center justify-center text-[#2D71F8] mr-1">
                            <i class="ri-calendar-line text-current text-md"></i>
                        </div>
                    </div>
                    <div class="flex items-center justify-between h-[70%] w-32 rounded-full border border-slate-300 bg-transparent">
                        <div class="text-sm font-medium text-slate-700 pl-3">This Month</div>
                        <div
                            class="icon w-7 h-7 bg-[#f0f7ff] rounded-full flex items-center justify-center text-[#2D71F8] mr-1">
                            <i class="ri-calendar-line text-current text-md"></i>
                        </div>
                    </div>
                    <div class="flex items-center justify-between h-[70%] w-32 rounded-full border border-slate-300 bg-transparent">
                        <div class="text-sm font-medium text-slate-700 pl-3">3 Month</div>
                        <div
                            class="icon w-7 h-7 bg-[#f0f7ff] rounded-full flex items-center justify-center text-[#2D71F8] mr-1">
                            <i class="ri-calendar-line text-current text-md"></i>
                        </div>
                    </div>
                    <div class="flex items-center justify-between h-[70%] w-32 rounded-full border border-slate-300 bg-transparent">
                        <div class="text-sm font-medium text-slate-700 pl-3">6 Month</div>
                        <div
                            class="icon w-7 h-7 bg-[#f0f7ff] rounded-full flex items-center justify-center text-[#2D71F8] mr-1">
                            <i class="ri-calendar-line text-current text-md"></i>
                        </div>
                    </div>
                    <div class="flex items-center justify-between h-[70%] w-24 rounded-full border border-slate-300 bg-transparent">
                        <div class="text-sm font-medium text-slate-700 pl-3">1 Year</div>
                        <div
                            class="icon w-7 h-7 bg-[#f0f7ff] rounded-full flex items-center justify-center text-[#2D71F8] mr-1">
                            <i class="ri-calendar-line text-current text-md"></i>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-center gap-3 pr-2">
                    <div class="text-sm font-medium text-slate-700 pl-3">Marugame Udon | <span class="text-gray-500">Grand Indonesia Mall</span></div>
                    <div class="icon w-7 h-7 bg-[#f0f7ff] rounded-full flex items-center justify-center text-[#2D71F8] mr-1">
                        <i class="ri-map-pin-line text-current text-md"></i>
                    </div>
                </div>
            </div>
            <div class="sales-data-cards">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 w-full">
                    <div class="flex flex-col h-full rounded-3xl bg-white">
                        <div class="w-full h-[72%] pt-3 px-3 flex flex-col justify-between items-center border-b border-neutral-100 pb-3">
                            <div class="flex flex-row justify-start items-center w-full h-20">
                                <div class="icon w-9 h-9 bg-[#f0f7ff] rounded-full flex items-center justify-center text-[#2D71F8] mr-3">
                                    <i class="ri-line-chart-line text-current text-lg"></i>
                                </div>
                                <div class="text-md font-medium text-slate-600">Total Sales Amount</div>
                            </div>
                            <div class="flex flex-row justify-between items-end w-full h-full">
                                <div class="text-2xl md:text-3xl font-medium text-slate-700 shimmer">12.303,00</div>
                                <div class="text-md text-gray-400 font-medium">IDR</div>
                            </div>
                        </div>
                        <div class="flex flex-row w-full h-[27%] justify-between items-center px-3">
                            <div class="flex items-center justify-center h-[68%] w-auto px-3 bg-[#f2fff9] rounded-full">
                                <div class="text-xs font-medium text-[#1C8370]">+IDR 1.000.000,00</div>
                            </div>
                            <div
                                class="flex items-center justify-center h-[68%] w-auto pl-3 pr-1 bg-[#f2fff9] rounded-full">
                                <div class="text-xs font-medium text-[#1C8370]">13.4 %</div>
                                <i class="ri-arrow-drop-up-fill text-[#1C8370] text-xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col h-full rounded-3xl bg-white">
                        <div class="w-full h-[72%] pt-3 px-3 flex flex-col justify-between items-center border-b border-neutral-100 pb-3">
                            <div class="flex flex-row justify-start items-center w-full h-20">
                                <div class="icon w-9 h-9 bg-[#f0f7ff] rounded-full flex items-center justify-center text-[#2D71F8] mr-3">
                                    <i class="ri-box-2-line text-current text-lg"></i>
                                </div>
                                <div class="text-md font-medium text-slate-600">Total Product Sales</div>
                            </div>
                            <div class="flex flex-row justify-between items-end w-full h-full">
                                <div class="text-2xl md:text-3xl font-medium text-slate-700 shimmer">8.121</div>
                                <div class="text-md text-gray-400 font-medium">Items</div>
                            </div>
                        </div>
                        <div class="flex flex-row w-full h-[27%] justify-between items-center px-3">
                            <div class="flex items-center justify-center h-[68%] w-auto px-3 bg-[#f2fff9] rounded-full">
                                <div class="text-xs font-medium text-[#1C8370]">+290 Items</div>
                            </div>
                            <div class="flex items-center justify-center h-[68%] w-auto pl-3 pr-1 bg-[#f2fff9] rounded-full">
                                <div class="text-xs font-medium text-[#1C8370]">3.8 %</div>
                                <i class="ri-arrow-drop-up-fill text-[#1C8370] text-xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col h-full rounded-3xl bg-white">
                        <div class="w-full h-[72%] pt-3 px-3 flex flex-col justify-between items-center border-b border-neutral-100 pb-3">
                            <div class="flex flex-row justify-start items-center w-full h-20">
                                <div class="icon w-9 h-9 bg-[#f0f7ff] rounded-full flex items-center justify-center text-[#2D71F8] mr-3">
                                    <i class="ri-group-line text-current text-lg"></i>
                                </div>
                                <div class="text-md font-medium text-slate-600">Total Guests</div>
                            </div>
                            <div class="flex flex-row justify-between items-end w-full h-full">
                                <div class="text-2xl md:text-3xl font-medium text-slate-700 shimmer">241</div>
                                <div class="text-md text-gray-400 font-medium">Persons</div>
                            </div>
                        </div>
                        <div class="flex flex-row w-full h-[27%] justify-between items-center px-3">
                            <div class="flex items-center justify-center h-[68%] w-auto px-3 bg-[#fff5f5] rounded-full">
                                <div class="text-xs font-medium text-[#FC4A4A]">-83 Persons</div>
                            </div>
                            <div class="flex items-center justify-center h-[68%] w-auto pl-3 pr-1 bg-[#fff5f5] rounded-full">
                                <div class="text-xs font-medium text-[#FC4A4A]">5.4 %</div>
                                <i class="ri-arrow-drop-down-fill text-[#FC4A4A] text-xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col h-full rounded-3xl bg-white">
                        <div class="w-full h-[72%] pt-3 px-3 flex flex-col justify-between items-center border-b border-neutral-100 pb-3">
                            <div class="flex flex-row justify-start items-center w-full h-20">
                                <div class="icon w-9 h-9 bg-[#f0f7ff] rounded-full flex items-center justify-center text-[#2D71F8] mr-3">
                                    <i class="ri-secure-payment-line text-current text-lg"></i>
                                </div>
                                <div class="text-md font-medium text-slate-600">Net Profits</div>
                            </div>
                            <div class="flex flex-row justify-between items-end w-full h-full">
                                <div class="text-2xl md:text-3xl font-medium text-slate-700 shimmer">34.000.000,00</div>
                                <div class="text-md text-gray-400 font-medium">IDR</div>
                            </div>
                        </div>
                        <div class="flex flex-row w-full h-[27%] justify-between items-center px-3">
                            <div class="flex items-center justify-center h-[68%] w-auto px-3 bg-[#f2fff9] rounded-full">
                                <div class="text-xs font-medium text-[#1C8370]">+IDR 23.000.000,00</div>
                            </div>
                            <div class="flex items-center justify-center h-[68%] w-auto pl-3 pr-1 bg-[#f2fff9] rounded-full">
                                <div class="text-xs font-medium text-[#1C8370]">36.1 %</div>
                                <i class="ri-arrow-drop-up-fill text-[#1C8370] text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="more-reports">
                <div class="flex w-full flex-col lg:flex-row gap-4 h-auto">
                    <div class="fav-product flex flex-col h-auto lg:h-[28rem] w-full lg:w-[30%] bg-white rounded-3xl px-4">
                        <div class="flex w-full h-16 items-center gap-3 flex-row pl-3">
                            <div class="w-2 h-2 rounded-full bg-[#2D71F8] mt-1"></div>
                            <div class="text-lg md:text-xl text-slate-700 font-normal">Favorite Product</div>
                        </div>
                        <div class="flex flex-row w-full h-11 gap-2 mb-3">
                            <div class="image-label text-xs md:text-sm flex items-center justify-center px-3 md:px-5 rounded-2xl text-slate-500 border border-grey-400">Img</div>
                            <div class="image-label text-xs md:text-sm flex-1 flex items-center justify-center rounded-2xl text-slate-500 border border-grey-400">Product Name</div>
                            <div class="image-label text-xs md:text-sm flex items-center justify-center px-2 md:px-4 rounded-2xl text-slate-500 border border-grey-400">Orders</div>
                        </div>
                        <div class="flex flex-col w-full h-auto max-h-64 md:max-h-none md:h-[19rem] overflow-y-auto space-y-3 py-2">
                            <div class="flex flex-row w-full h-16 md:h-20 items-center border-b border-zinc-200 justify-between pb-3">
                                <div class="p_image flex items-center justify-center bg-zinc-100 w-10 h-10 md:w-14 md:h-14 rounded-xl"></div>
                                <div class="flex flex-col items-center justify-center">
                                    <div class="text-xs md:text-sm font-medium text-slate-700 pb-1 truncate max-w-[100px] md:max-w-none">Chicken Katsu Curry Udon</div>
                                    <div class="flex items-center justify-center px-3 py-1 bg-[#fff0f6] text-[#db005b] rounded-xl text-xs font-medium">Udon</div>
                                </div>
                                <div class="text-sm md:text-md text-slate-700 font-medium pr-3 md:pr-7">137 <span class="font-normal text-zinc-400">Times</span></div>
                            </div>
                            <div class="flex flex-row w-full h-16 md:h-20 items-center border-b border-zinc-200 justify-between pb-3">
                                <div class="p_image flex items-center justify-center bg-zinc-100 w-10 h-10 md:w-14 md:h-14 rounded-xl"></div>
                                <div class="flex flex-col items-center justify-center">
                                    <div class="text-xs md:text-sm font-medium text-slate-700 pb-1 truncate max-w-[100px] md:max-w-none">Beef Teriyaki Ramen</div>
                                    <div class="flex items-center justify-center px-3 py-1 bg-[#f0fff4] text-[#00b86b] rounded-xl text-xs font-medium">Ramen</div>
                                </div>
                                <div class="text-sm md:text-md text-slate-700 font-medium pr-3 md:pr-7">125 <span class="font-normal text-zinc-400">Times</span></div>
                            </div>
                            <div class="flex flex-row w-full h-16 md:h-20 items-center border-b border-zinc-200 justify-between pb-3">
                                <div class="p_image flex items-center justify-center bg-zinc-100 w-10 h-10 md:w-14 md:h-14 rounded-xl"></div>
                                <div class="flex flex-col items-center justify-center">
                                    <div class="text-xs md:text-sm font-medium text-slate-700 pb-1 truncate max-w-[100px] md:max-w-none">Salmon Sushi Roll</div>
                                    <div class="flex items-center justify-center px-3 py-1 bg-[#fff7f0] text-[#ff7a00] rounded-xl text-xs font-medium">Sushi</div>
                                </div>
                                <div class="text-sm md:text-md text-slate-700 font-medium pr-3 md:pr-7">98 <span class="font-normal text-zinc-400">Times</span></div>
                            </div>
                            <div class="flex flex-row w-full h-16 md:h-20 items-center border-b border-zinc-200 justify-between pb-3">
                                <div class="p_image flex items-center justify-center bg-zinc-100 w-10 h-10 md:w-14 md:h-14 rounded-xl"></div>
                                <div class="flex flex-col items-center justify-center">
                                    <div class="text-xs md:text-sm font-medium text-slate-700 pb-1 truncate max-w-[100px] md:max-w-none">Salmon Sushi Roll</div>
                                    <div class="flex items-center justify-center px-3 py-1 bg-[#fff7f0] text-[#ff7a00] rounded-xl text-xs font-medium">Sushi</div>
                                </div>
                                <div class="text-sm md:text-md text-slate-700 font-medium pr-3 md:pr-7">98 <span class="font-normal text-zinc-400">Times</span></div>
                            </div>
                            <div class="flex flex-row w-full h-16 md:h-20 items-center border-b border-zinc-200 justify-between pb-3">
                                <div class="p_image flex items-center justify-center bg-zinc-100 w-10 h-10 md:w-14 md:h-14 rounded-xl"></div>
                                <div class="flex flex-col items-center justify-center">
                                    <div class="text-xs md:text-sm font-medium text-slate-700 pb-1 truncate max-w-[100px] md:max-w-none">Salmon Sushi Roll</div>
                                    <div class="flex items-center justify-center px-3 py-1 bg-[#fff7f0] text-[#ff7a00] rounded-xl text-xs font-medium">Sushi</div>
                                </div>
                                <div class="text-sm md:text-md text-slate-700 font-medium pr-3 md:pr-7">98 <span class="font-normal text-zinc-400">Times</span></div>
                            </div>
                        </div>
                    </div>
                    <div class="fav-product flex flex-col h-auto lg:h-[28rem] w-full lg:w-[70%] bg-white rounded-3xl px-4">
                        <div class="flex w-full justify-between h-16 items-center gap-3 flex-row pl-3">
                            <div class="flex flex-row justify-center items-center gap-3">
                                <div class="w-2 h-2 rounded-full bg-[#2D71F8] mt-1"></div>
                                <div class="text-lg md:text-xl text-slate-700 font-normal">All Orders</div>
                            </div>
                            <div class="flex flex-row justify-center items-center mr-5">
                                <div class="flex flex-row h-[70%] w-[97%] ">
                                    <div class="flex items-center w-[94%] pl-1 h-[60%] bg-white rounded-full mx-4 border">
                                        <div class="icon flex items-center justify-center w-8 h-8 bg-gray-100 rounded-full shrink-0 text-gray-700">
                                            <i class="bi bi-search text-sm text-current"></i>
                                        </div>
                                        <input v-model="searchQuery" type="text" class="flex-1 bg-transparent px-4 placeholder:text-gray-400 text-gray-700 font-semibold border-none focus:outline-none focus:ring-0" placeholder="Search Order"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-row w-full h-12 gap-2 mb-3 overflow-x-auto pb-2">
                            <div class="label text-xs md:text-sm flex-shrink-0 flex items-center justify-center min-w-16 md:min-w-24 px-2 md:px-4 rounded-2xl text-slate-500 border border-grey-400">#</div>
                            <div class="label text-xs md:text-sm flex-shrink-0 flex items-center justify-center min-w-14 md:min-w-18 px-2 md:px-4 rounded-2xl text-slate-500 border border-grey-400">Order</div>
                            <div class="label text-xs md:text-sm flex-shrink-0 flex items-center justify-center min-w-24 md:min-w-36 px-2 md:px-4 rounded-2xl text-slate-500 border border-grey-400">Cashier</div>
                            <div class="label text-xs md:text-sm flex-shrink-0 flex items-center justify-center min-w-28 md:min-w-40 px-2 md:px-4 rounded-2xl text-slate-500 border border-grey-400">Date & Time</div>
                            <div class="label text-xs md:text-sm flex-shrink-0 flex items-center justify-center min-w-16 md:min-w-24 px-2 md:px-4 rounded-2xl text-slate-500 border border-grey-400">Guests</div>
                            <div class="label text-xs md:text-sm flex-shrink-0 flex items-center justify-center min-w-32 md:min-w-44 px-2 md:px-4 rounded-2xl text-slate-500 border border-grey-400">Total Payment</div>
                            <div class="label text-xs md:text-sm flex-shrink-0 flex items-center justify-center min-w-16 md:min-w-24 px-2 md:px-4 rounded-2xl text-slate-500 border border-grey-400">Status</div>
                            <div class="label text-xs md:text-sm flex-shrink-0 flex items-center justify-center min-w-20 md:min-w-28 px-2 md:px-4 rounded-2xl text-slate-500 border border-grey-400">Detail</div>
                        </div>
                        <div class="flex flex-col w-full h-auto max-h-64 md:max-h-[20rem] overflow-y-auto">
                            <div class="w-full overflow-x-auto">
                                <div class="flex flex-row w-max lg:w-full h-10 md:h-[2.5rem] gap-2 mb-3">
                                    <div class="label text-xs md:text-sm flex-shrink-0 flex items-center justify-center min-w-16 md:min-w-24 px-2 md:px-4 rounded-2xl text-slate-700 font-medium">MAY128</div>
                                    <div class="label text-[0.6rem] md:text-[0.73rem] flex-shrink-0 flex items-center justify-center w-12 md:w-16 text-center px-2 md:px-4 rounded-2xl bg-[#f0f7ff] text-[#2D71F8] font-medium">Take Away</div>
                                    <div class="label text-xs md:text-sm flex-shrink-0 flex items-center justify-center min-w-24 md:min-w-36 px-2 md:px-4 rounded-2xl text-slate-700 font-medium truncate">Joko Susanto</div>
                                    <div class="label text-xs md:text-sm flex-shrink-0 flex items-center justify-center min-w-28 md:min-w-40 px-2 md:px-4 rounded-2xl text-slate-700 font-medium">2025-04-09 01:37</div>
                                    <div class="label text-xs md:text-sm flex-shrink-0 flex items-center justify-center min-w-16 md:min-w-24 px-2 md:px-4 rounded-2xl text-slate-700 font-medium">1</div>
                                    <div class="label text-xs md:text-sm flex-shrink-0 flex items-center justify-center min-w-32 md:min-w-44 px-2 md:px-4 rounded-2xl text-slate-700 font-medium">Rp36.500</div>
                                    <div class="label text-xs md:text-sm flex-shrink-0 flex items-center justify-center min-w-16 md:min-w-24 px-2 md:px-4 rounded-2xl text-slate-700 font-medium">Status</div>
                                    <div class="label text-xs md:text-sm flex-shrink-0 flex items-center justify-center min-w-20 md:min-w-28 px-2 md:px-4 rounded-2xl text-[#2D71F8] underline font-medium">Detail</div>
                                </div>
                                <div class="flex flex-row w-max lg:w-full h-10 md:h-[2.5rem] gap-2 mb-3">
                                    <div class="label text-xs md:text-sm flex-shrink-0 flex items-center justify-center min-w-16 md:min-w-24 px-2 md:px-4 rounded-2xl text-slate-700 font-medium">MAY128</div>
                                    <div class="label text-[0.6rem] md:text-[0.73rem] flex-shrink-0 flex items-center justify-center w-12 md:w-16 text-center px-2 md:px-4 rounded-2xl bg-[#f0f7ff] text-[#2D71F8] font-medium">Take Away</div>
                                    <div class="label text-xs md:text-sm flex-shrink-0 flex items-center justify-center min-w-24 md:min-w-36 px-2 md:px-4 rounded-2xl text-slate-700 font-medium truncate">Joko Susanto</div>
                                    <div class="label text-xs md:text-sm flex-shrink-0 flex items-center justify-center min-w-28 md:min-w-40 px-2 md:px-4 rounded-2xl text-slate-700 font-medium">2025-04-09 01:37</div>
                                    <div class="label text-xs md:text-sm flex-shrink-0 flex items-center justify-center min-w-16 md:min-w-24 px-2 md:px-4 rounded-2xl text-slate-700 font-medium">1</div>
                                    <div class="label text-xs md:text-sm flex-shrink-0 flex items-center justify-center min-w-32 md:min-w-44 px-2 md:px-4 rounded-2xl text-slate-700 font-medium">Rp36.500</div>
                                    <div class="label text-xs md:text-sm flex-shrink-0 flex items-center justify-center min-w-16 md:min-w-24 px-2 md:px-4 rounded-2xl text-slate-700 font-medium">Status</div>
                                    <div class="label text-xs md:text-sm flex-shrink-0 flex items-center justify-center min-w-20 md:min-w-28 px-2 md:px-4 rounded-2xl text-[#2D71F8] underline font-medium">Detail</div>
                                </div>
                                <div class="flex flex-row w-max lg:w-full h-10 md:h-[2.5rem] gap-2 mb-3">
                                    <div class="label text-xs md:text-sm flex-shrink-0 flex items-center justify-center min-w-16 md:min-w-24 px-2 md:px-4 rounded-2xl text-slate-700 font-medium">MAY128</div>
                                    <div class="label text-[0.6rem] md:text-[0.73rem] flex-shrink-0 flex items-center justify-center w-12 md:w-16 text-center px-2 md:px-4 rounded-2xl bg-[#f0f7ff] text-[#2D71F8] font-medium">Take Away</div>
                                    <div class="label text-xs md:text-sm flex-shrink-0 flex items-center justify-center min-w-24 md:min-w-36 px-2 md:px-4 rounded-2xl text-slate-700 font-medium truncate">Joko Susanto</div>
                                    <div class="label text-xs md:text-sm flex-shrink-0 flex items-center justify-center min-w-28 md:min-w-40 px-2 md:px-4 rounded-2xl text-slate-700 font-medium">2025-04-09 01:37</div>
                                    <div class="label text-xs md:text-sm flex-shrink-0 flex items-center justify-center min-w-16 md:min-w-24 px-2 md:px-4 rounded-2xl text-slate-700 font-medium">1</div>
                                    <div class="label text-xs md:text-sm flex-shrink-0 flex items-center justify-center min-w-32 md:min-w-44 px-2 md:px-4 rounded-2xl text-slate-700 font-medium">Rp36.500</div>
                                    <div class="label text-xs md:text-sm flex-shrink-0 flex items-center justify-center min-w-16 md:min-w-24 px-2 md:px-4 rounded-2xl text-slate-700 font-medium">Status</div>
                                    <div class="label text-xs md:text-sm flex-shrink-0 flex items-center justify-center min-w-20 md:min-w-28 px-2 md:px-4 rounded-2xl text-[#2D71F8] underline font-medium">Detail</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>