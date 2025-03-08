<script setup>
import { ref, onMounted } from "vue";

const props = defineProps({
    product: Array,
    kategori: Array,
    namaKasir: String,
})

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

const getProductCount = (categoryId) => {
  return props.product.filter(product => product.kategoris_id === categoryId).length;
};

onMounted(() => {
    updateDateTime();
    setInterval(updateDateTime, 1000);
    console.log("Produk:", props.product);
    console.log("Kategori:", props.kategori);
});
</script>
<style>
    * {
        .no-select {
            user-select: none;
        }   
    }
</style>
<template>
    <div class="flex flex-row h-screen w-full bg-[#F7F7F7] overflow-y-hidden no-select">
        <!-- kiri -->
        <div class="flex flex-col w-[80%] h-screen">
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
            <div class="flex flex-row h-52 w-[100%] px-4 py-4 gap-4 overflow-x-auto overflow-y-hidden whitespace-nowrap">
                <div @click="toggleActive('all')" :class="['flex flex-row py-3 px-3 w-48 h-20 rounded-2xl cursor-pointer',activeMenu === 'all' ? 'bg-[#f0f7ff] outline outline-2 outline-[#2D71F8]' : 'bg-white']">
                    <div :class="['category_icon flex justify-center items-center w-14 h-14 rounded-full',activeMenu === 'all' ? 'bg-[#2D71F8] text-white' : 'bg-gray-100 text-gray-500']">
                        <i class="ri-restaurant-2-line text-xl text-current"></i>
                    </div>
                    <div class="wrap flex flex-col ml-3 justify-center">
                        <div class="nama_kategori font-semibold text-lg text-gray-700">All Menu</div>
                        <div class="total_barang_in_kategori text-sm text-gray-500">{{ props.product.length }} items</div>
                    </div>
                </div>  
                <!-- Drinks -->
                <div v-for="kategori in kategori" :key="kategori.id" @click="toggleActive(kategori.id)" :class="['flex flex-row py-3 px-3 w-48 h-20 rounded-2xl cursor-pointer',activeMenu === kategori.id ? 'bg-[#f0f7ff] outline outline-2 outline-[#2D71F8]' : 'bg-white']">
                    <div :class="['category_icon flex justify-center items-center w-14 h-14 rounded-full',activeMenu === kategori.id ? 'bg-[#2D71F8] text-white' : 'bg-gray-100 text-gray-500']">
                        <i class="ri-cup-line text-xl text-current"></i>
                    </div>
                    <div class="wrap flex flex-col ml-3 justify-center">
                        <div class="nama_kategori font-semibold text-lg text-gray-700">{{ kategori.nama_kategori}}</div>
                        <div class="total_barang_in_kategori text-sm text-gray-500">{{ getProductCount(kategori.id) }} items</div>
                    </div>
                </div>
            </div>
            <!-- search -->
            <div class="flex items-center justify-center w-[97%] h-auto px-4 pr-2 bg-white rounded-full h-16 py-2 mx-auto">
                <input type="text" class="flex-1 bg-transparent px-4 placeholder:text-gray-400 text-gray-700 font-semibold border-none focus:outline-none focus:ring-0" placeholder="Search something sweet on your mind..."/>
                    <div class="icon flex items-center justify-center w-12 h-12 bg-gray-100 rounded-full shrink-0 text-gray-700">
                    <i class="bi bi-search text-lg text-current"></i>
                </div>
            </div>
            <div class="flex flex-wrap flex-row gap-[1.10rem] w-full px-4 py-4 mt-3 h-full pb-[15%] overflow-auto">
                <div v-for="product in product" :key="product.id" class="flex flex-col px-3 py-3 bg-white w-52 h-64 rounded-xl overflow-hidden">
                    <div class="img rounded-xl w-full h-[65%] bg-[#F6F6F6] flex justify-center items-center">
                        <img class="max-h-32 w-auto object-contain" :src="'http://127.0.0.1:8000/storage/' + product.foto_product" alt="">
                    </div>
                    <div class="nama_product text-medium font-semibold text-gray-700 pt-2 text-left">
                        {{ product.nama_product }}
                    </div>
                    <div class="flex flex-row w-full justify-between items-center mt-auto">
                        <div class="kategori flex items-center justify-center px-2 py-0.5 rounded-xl w-fit text-xs font-semibold" :style="{ backgroundColor: product.kategori?.warna_background_kategori, color: product.kategori?.warna_teks_kategori}">
                            {{product.kategori?.nama_kategori}}
                        </div>
                        <div class="harga text-gray-700 font-semibold text-lg">
                            Rp{{ product.harga_product }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex w-[25%] h-screen bg-white shadow-2xl shadow-slate-100">
            <p>o</p>
        </div>
    </div>
</template>
