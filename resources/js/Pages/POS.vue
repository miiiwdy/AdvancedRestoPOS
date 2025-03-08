<script setup>
import { ref, onMounted } from "vue";

const time = ref("");
const period = ref("");
const date = ref("");
const activeMenu = ref("all");

const toggleActive = (menu) => {
    if (activeMenu.value !== menu) {
        activeMenu.value = menu;
    }
};
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

onMounted(() => {
    updateDateTime();
    setInterval(updateDateTime, 1000);
});
</script>

<template>
    <div class="flex flex-row h-screen w-full bg-[#F8F8F8]">
        <!-- kiri -->
        <div class="flex flex-col w-[70%] h-screen">
            <div class="flex flex-row w-full px-4 py-4 pb-1 h-auto items-center gap-4">
                <div
                    class="hamburger-menu w-[3.2rem] h-[3.2rem] flex items-center justify-center rounded-full bg-white text-[#2D71F8]">
                    <i class="ri-menu-5-fill text-current text-xl"></i>
                </div>
                <div class="tanggal w-56 h-[3.2rem] rounded-full items-center bg-white flex">
                    <div class="wrap px-2 flex flex-row items-center">
                        <div
                            class="icon w-9 h-9 bg-[#f0f7ff] rounded-full flex items-center justify-center text-[#2D71F8]">
                            <i class="ri-calendar-line text-current text-lg"></i>
                        </div>
                        <p class="text-gray-700 font-semibold ml-3">{{ date }}</p>
                    </div>
                </div>
                <div class="strip text-2xl ">-</div>
                <div class="jam w-36 h-[3.2rem] rounded-full items-center bg-white flex">
                    <div class="wrap px-2 flex flex-row items-center">
                        <div
                            class="icon w-9 h-9 bg-[#f0f7ff] rounded-full flex items-center justify-center text-[#2D71F8]">
                            <i class="ri-time-line text-current text-lg"></i>
                        </div>
                        <p class="text-gray-700 font-semibold ml-3">{{ time }} <span class="text-gray-400">{{ period
                                }}</span></p>
                    </div>
                </div>
            </div>
            <div class="flex flex-row h-auto w-full px-4 py-4 gap-4">
                <div @click="toggleActive('all')" :class="['flex flex-col py-3 px-3 w-40 h-36 rounded-2xl justify-between cursor-pointer',activeMenu === 'all' ? 'bg-[#f0f7ff] outline outline-2 outline-[#2D71F8]' : 'bg-white']">
                    <div :class="['category_icon flex justify-center items-center w-14 h-14 rounded-full',activeMenu === 'all' ? 'bg-[#2D71F8] text-white' : 'bg-gray-100 text-gray-500']">
                        <i class="ri-restaurant-2-line text-xl text-current"></i>
                    </div>
                    <div class="wrap flex flex-col gap-1">
                        <div class="nama_kategori font-semibold text-gray-700">All Menu</div>
                        <div class="total_barang_in_kategori text-sm text-gray-500">8 items</div>
                    </div>
                </div>

                <!-- Drinks -->
                <div @click="toggleActive('drinks')" :class="['flex flex-col py-3 px-3 w-40 h-36 rounded-2xl justify-between cursor-pointer',activeMenu === 'drinks' ? 'bg-[#f0f7ff] outline outline-2 outline-[#2D71F8]' : 'bg-white']">
                    <div :class="['category_icon flex justify-center items-center w-14 h-14 rounded-full',activeMenu === 'drinks' ? 'bg-[#2D71F8] text-white' : 'bg-gray-100 text-gray-500']">
                        <i class="ri-cup-line text-xl text-current"></i>
                    </div>
                    <div class="wrap flex flex-col gap-1">
                        <div class="nama_kategori font-semibold text-gray-700">Drinks</div>
                        <div class="total_barang_in_kategori text-sm text-gray-500">3 items</div>
                    </div>
                </div>
                <!-- Dessert -->
                <div @click="toggleActive('dessert')" :class="['flex flex-col py-3 px-3 w-40 h-36 rounded-2xl justify-between cursor-pointer',activeMenu === 'dessert' ? 'bg-[#f0f7ff] outline outline-2 outline-[#2D71F8]' : 'bg-white']">
                    <div :class="['category_icon flex justify-center items-center w-14 h-14 rounded-full',activeMenu === 'dessert' ? 'bg-[#2D71F8] text-white' : 'bg-gray-100 text-gray-500']">
                        <i class="ri-cake-3-line text-xl text-current"></i>
                    </div>
                    <div class="wrap flex flex-col gap-1">
                        <div class="nama_kategori font-semibold text-gray-700">Dessert</div>
                        <div class="total_barang_in_kategori text-sm text-gray-500">3 items</div>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-center w-[97%] h-auto px-4 bg-white rounded-full h-16 py-2 mx-auto">
            <input type="text" class="flex-1 bg-transparent px-4 placeholder:text-gray-400 text-gray-700 font-semibold border-none focus:outline-none focus:ring-0" placeholder="Search something sweet on your mind..."/>
            <div class="icon flex items-center justify-center w-12 h-12 bg-gray-100 rounded-full shrink-0 text-gray-700">
                <i class="bi bi-search text-lg text-current"></i>
            </div>
        </div>

        </div>
        <div class="flex w-[30%] h-screen bg-white shadow-2xl shadow-slate-100">
            <p>o</p>
        </div>
    </div>
</template>
