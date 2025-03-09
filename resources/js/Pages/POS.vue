    <script setup>
    import { ref, onMounted, computed, watch } from "vue";

    const props = defineProps({
        product: Array,
        kategori: Array,
        namaKasir: String,
        pajak: Number,
    })

    onMounted(() => {
        updateDateTime();
        setInterval(updateDateTime, 1000);
        typeEffect();
    });

    const time = ref("");
    const period = ref("");
    const date = ref("");
    const activeMenu = ref(1337);
    const searchQuery = ref('');
    const isModalOpen = ref(false);
    const isCartNoteModalOpen = ref(false);
    const selectedProduct = ref(null);
    const selectedCartProduct = ref(null);
    const quantity = ref(1)
    var note = ref('');
    const cart = ref([]);
    const showCart = ref(true);
    const guest = ref(0);
    const payment = ref('Payment');
    const placeholderText = "Search something sweet on your mind here...";
    const displayPlaceholder = ref("");
    var isCooldown = false;
    var index = 0;
    var isDeleting = false;

    const toggleCart = () => {
    showCart.value = !showCart.value;
    };

    const openModal = (product) => {
        selectedProduct.value = product;
        isModalOpen.value = true;
    };

    const closeModal = () => {
        isModalOpen.value = false;
        selectedProduct.value = null;
        note.value = '';
        quantity.value = 1;
    };
    const openCartNoteModal = (item) => {
        if (isCartNoteModalOpen.value && selectedCartProduct.value === item) {
            isCartNoteModalOpen.value = false;
            selectedCartProduct.value = null;
        } else {
            selectedCartProduct.value = item;
            isCartNoteModalOpen.value = true;
        }
    };

    const saveNote = () => {
        if (selectedCartProduct.value) {
            selectedCartProduct.value.note = note.value;
        }
        isCartNoteModalOpen.value = false;
        selectedCartProduct.value = null;
    }

    const closeCartNoteModal = () => {
        isCartNoteModalOpen.value = false;
        selectedCartProduct.value = null;
        note.value = '';
    };

    watch(selectedCartProduct, (newValue) => {
        note.value = newValue && newValue.note ? newValue.note : '';
    }, { deep: true });

    const increaseQty = () => {
        quantity.value++
    };

    const decreaseQty = () => {
        if (quantity.value > 1) {
            quantity.value--;
        }
    };

    const calculateSubtotal = () => cart.value.reduce((sum, item) => sum + item.tt_b, 0);
    const calculateTotalPajak = () => cart.value.reduce((sum, item) => sum + item.total_pajak, 0);
    const calculateTotal = () => cart.value.reduce((sum, item) => sum + item.tt_a, 0);
    
    const addToCart = () => {
        if (selectedProduct.value) {
            const totalHargaPerItem = selectedProduct.value.harga_product;
            const totalHargaBefore = selectedProduct.value.harga_product * quantity.value;
            const existingProductIndex = cart.value.findIndex(item => item.kode_product === selectedProduct.value.kode_product);
            const totalPajakPerItem =  totalHargaBefore * (props.pajak / 100);
            const totalHargaAfter = totalHargaBefore + totalPajakPerItem;

            if (existingProductIndex !== -1) {
                alert('produk sudah ada di keranjang')
                return;
            }
            cart.value.push({
                np: selectedProduct.value.nama_product,
                kode_product: selectedProduct.value.kode_product,
                deskripsi_product: selectedProduct.value.deskripsi_product,
                foto_product: selectedProduct.value.foto_product,
                kategori: selectedProduct.value.kategoris_id,
                quantity: quantity.value,
                hb: selectedProduct.value.harga_beli_product,
                hj: totalHargaPerItem,
                tt_b: totalHargaBefore,
                tt_a: totalHargaAfter,
                total_pajak: totalPajakPerItem,
                note: note.value || '',
            })
            console.log(cart);
            closeModal();
            quantity.value = 1;
        }
    }

    const removeFromCart = (index) => {
        if (isCooldown) {      
            console.log('cooldown');
            return;
        }

        if (cart.value.length > 0) {
            isCooldown = true;
            const removedItem = cart.value.splice(index, 1)[0];
            const totalHargaSebelumDiskonPajak = cart.value.reduce((total, item) => {
                return total + item.tt_b;
            }, 0);

            console.log('Barang dihapus:', removedItem);
            console.log('Total harga setelah penghapusan:', totalHargaSebelumDiskonPajak);

            setTimeout(() => {
                isCooldown = false;
                console.log('Cooldown selesai, Anda dapat menghapus barang lagi.');
            }, 1000);
        } else {
            console.log('Keranjang kosong, tidak ada yang bisa dihapus.');
        }
    };

    const filteredAndSortedProducts = computed(() => {
        return Array.isArray(props.product) ? props.product.filter(barang => {
            const query = searchQuery.value.toLowerCase();
            const matchesSearch = barang.nama_product.toLowerCase().includes(query);
            if (activeMenu.value === 1337) {
                return matchesSearch;
            }
            const matchesCategory = activeMenu.value ? barang.kategoris_id === activeMenu.value : true;
            return matchesSearch && matchesCategory;
        }) : [];
    });

    const toggleActive = (categoryId) => {
        activeMenu.value = categoryId;
    };
    const formatCurrency = (value) => {
        if (!value) return "0";
        return Number(value).toLocaleString('id-ID');
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

    const typeEffect = () => {
        if (!isDeleting && index < placeholderText.length) {
            displayPlaceholder.value += placeholderText[index];
            index++;
        } else if (isDeleting && index > 0) {
            displayPlaceholder.value = displayPlaceholder.value.slice(0, -1);
            index--;
        }
        
        if (index === placeholderText.length) {
            isDeleting = true;
            setTimeout(typeEffect, 400);
        } else if (index === 0) {
            isDeleting = false;
            setTimeout(typeEffect, 100);
        } else {
            setTimeout(typeEffect, isDeleting ? 50 : 100);
        }
    };

    </script>
    <style>
        * {
            .no-select {
                user-select: none;
            }   
        }
        .slide-enter-active, .slide-leave-active {
            transition: transform 0.3s ease-in-out;
        }
        .slide-enter-from, .slide-leave-to {
            transform: translateX(100%);
        }
        .slide-enter-to, .slide-leave-from {
            transform: translateX(0);
        }
    </style>
    <template>
        <div class="flex flex-row h-screen w-full bg-[#F9F9F9]  overflow-y-hidden no-select">
            <!-- kiri -->
            <div class="flex flex-col h-screen" :class="showCart ? 'w-[76%]' : 'w-full'">
                <div class="flex flex-row w-full px-4 py-4 pb-1 h-auto items-center gap-4 justify-between">
                    <div class="flex gap-4">
                        <div class="hamburger-menu w-[3.2rem] h-[3.2rem] flex items-center justify-center rounded-full bg-white text-[#2D71F8] cursor-pointer">
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
                        <div class="jam w-36 h-[3.2rem] rounded-full items-center bg-white flex">
                        <div class="wrap px-2 flex flex-row items-center">
                            <div
                            class="icon w-9 h-9 bg-[#f0f7ff] rounded-full flex items-center justify-center text-[#2D71F8]">
                            <i class="ri-time-line text-current text-lg"></i>
                        </div>
                        <p class="text-gray-700 font-semibold ml-3">{{ time }} <span class="text-gray-400">{{ period }}</span></p>
                    </div>
                        </div>
                    </div>
                <div v-if="!showCart" @click="toggleCart" class="cart-btn-before w-[3.2rem] h-[3.2rem] flex items-center justify-center rounded-full bg-white text-[#2D71F8] cursor-pointer">
                    <i class="ri-file-list-3-line text-current text-xl"></i>
                </div>
                </div>
                <div class="flex flex-row h-52 w-[100%] px-4 py-4 gap-4 overflow-x-auto overflow-y-hidden whitespace-nowrap">
                    <div @click="toggleActive(1337)" :class="['flex flex-row py-3 px-3 w-48 h-20 rounded-2xl cursor-pointer',activeMenu === 1337 ? 'bg-[#f0f7ff] outline outline-2 outline-[#2D71F8]' : 'bg-white']">
                        <div :class="['category_icon flex justify-center items-center w-14 h-14 rounded-full',activeMenu === 1337 ? 'bg-[#2D71F8] text-white' : 'bg-gray-100 text-gray-500']">
                            <i class="ri-restaurant-2-line text-xl text-current"></i>
                        </div>
                        <div class="wrap flex flex-col ml-3 justify-center">
                            <div class="nama_kategori font-semibold text-lg text-gray-700">All Menu</div>
                            <div class="total_barang_in_kategori text-sm text-gray-500">{{ props.product.length }} items</div>
                        </div>
                    </div>  
                    <div v-for="kategori in kategori" :key="kategori.id" @click="toggleActive(kategori.id)" :class="['flex flex-row py-3 px-3 w-48 h-20 rounded-2xl cursor-pointer',activeMenu === kategori.id ? 'bg-[#f0f7ff] outline outline-2 outline-[#2D71F8]' : 'bg-white']">
                        <div :class="['category_icon flex justify-center items-center w-14 h-14 rounded-full',activeMenu === kategori.id ? 'bg-[#2D71F8] text-white' : 'bg-gray-100 text-gray-500']">
                            <i class="text-xl text-current" :class="[kategori.icon]"></i>
                        </div>
                        <div class="wrap flex flex-col ml-3 justify-center">
                            <div class="nama_kategori font-semibold text-lg text-gray-700">{{ kategori.nama_kategori}}</div>
                            <div class="total_barang_in_kategori text-sm text-gray-500">{{ getProductCount(kategori.id) }} items</div>
                        </div>
                    </div>
                </div>
                <!-- search -->
                <div class="flex items-center justify-center w-[97%] h-auto px-4 pr-2 bg-white rounded-full h-16 py-2 mx-auto">
                    <input v-model="searchQuery" type="text" class="flex-1 bg-transparent px-4 placeholder:text-gray-400 text-gray-700 font-semibold border-none focus:outline-none focus:ring-0" :placeholder="displayPlaceholder"/>
                        <div class="icon flex items-center justify-center w-12 h-12 bg-gray-100 rounded-full shrink-0 text-gray-700">
                        <i class="bi bi-search text-lg text-current"></i>
                    </div>
                </div>
                <div class="flex flex-wrap flex-row gap-[1.20rem] w-full px-4 py-4 mt-3 h-full pb-[15%] overflow-auto">
                    <div v-for="product in filteredAndSortedProducts" :key="product.id" @click="openModal(product)" class="flex flex-col px-3 py-3 bg-white w-52 h-64 rounded-xl overflow-hidden cursor-pointer">
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
                                Rp{{ formatCurrency(product.harga_product) }}
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal -->
                <div v-if="isModalOpen" class="fixed inset-0 flex items-center justify-center bg-slate-400 bg-opacity-50" @click.self="closeModal">
                    <div class="absolute top-1/2 left-1/3 transform -translate-x-[7rem] -translate-y-1/2 bg-white rounded-2xl w-[26rem] shadow-2xl">
                        <div class="flex flex-row justify-between items-center justify-center shadow-lg shadow-gray-100 rounded-md p-3 pb-3">
                            <div class="flex w-9 h-9 bg-white"></div>
                            <h2 class="text-normal font-normal">Detail Produk</h2>
                            <div class="flex items-center justify-center rounded-full bg-[#fff2f3] w-9 h-9 text-[#FC4A4A] cursor-pointer" @click="closeModal">
                                <i class="bi bi-x-lg text-current"></i>
                            </div>
                        </div>
                        <div v-if="selectedProduct">
                            <div class="mt-3 img rounded-2xl w-[95%] m-auto h-44 bg-[#F5F5F5] flex justify-center items-center">
                                <img class="max-h-32 w-auto object-contain" :src="'http://127.0.0.1:8000/storage/' + selectedProduct.foto_product" alt="{{ selectedProduct.kode_product }} {{ selectedProduct.deskripsi_product }}">
                            </div>
                            <div class="flex flex-col mt-1 p-3">
                                <div class="kategori flex items-center justify-center px-2 py-0.5 rounded-xl w-fit text-xs font-semibold" :style="{ backgroundColor: selectedProduct.kategori?.warna_background_kategori, color: selectedProduct.kategori?.warna_teks_kategori}"> {{selectedProduct.kategori?.nama_kategori}}
                                </div>
                                <p class="mt-2 text-gray-700 font-semibold text-lg">{{ selectedProduct.nama_product }}</p>
                                <p class="text-gray-500 text-sm">{{ selectedProduct.deskripsi_product }}</p>
                                <p class="text-[#2D71F8] font-medium text-xl mt-2">Rp{{ formatCurrency(selectedProduct.harga_product) }}</p>
                                <textarea v-model="note" class="flex justify-start items-start mt-4 w-full h-16 bg-[#F5F5F5] text-gray-700 text-xs border-none focus:outline-none focus:ring-0 rounded-2xl p-2 text-left align-top placeholder:text-left" placeholder="Add notes to your order..." type="text" name="" id=""></textarea>
                            </div>
                            <div class="flex mt-1 w-full px-3 rounded-md h-auto shadow-[0px_-4px_6px_rgba(0,0,0,0.1)] shadow-gray-100">
                                <div class="mt-3 quantity w-full h-12 rounded-full bg-[#F5F5F5] flex flex-row justify-between items-center px-1">
                                    <div @click="decreaseQty" class="flex items-center justify-center bg-white w-10 h-10 rounded-full cursor-pointer">
                                        <i class="bi bi-dash"></i>
                                    </div>
                                    <div class="text-xl">{{ quantity }}</div>
                                    <div @click="increaseQty" class="flex items-center justify-center bg-white w-10 h-10 rounded-full cursor-pointer">
                                        <i class="bi bi-plus-lg"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end mt-3">
                            <button @click="addToCart" class="bg-[#2D71F8] w-full text-white px-4 py-4 rounded-b-2xl hover:bg-[#6196ff]">Add to Cart</button>
                        </div>
                    </div>
                </div>
                <!-- Cart note Modal -->
                <div v-if="isCartNoteModalOpen" class="fixed inset-0 flex items-center justify-center bg-slate-400 bg-opacity-50" @click.self="closeCartNoteModal">
                    <div class="absolute top-1/2 left-1/3 transform -translate-x-[7rem] -translate-y-1/2 bg-white rounded-2xl w-[26rem] shadow-2xl">
                        <div class="flex flex-row justify-between items-center justify-center shadow-lg shadow-gray-100 rounded-md p-3 pb-3">
                            <div class="flex w-9 h-9 bg-white"></div>
                            <h2 class="text-normal font-normal"> Item Notes</h2>
                            <div class="flex items-center justify-center rounded-full bg-[#fff2f3] w-9 h-9 text-[#FC4A4A] cursor-pointer" @click="closeCartNoteModal">
                                <i class="bi bi-x-lg text-current"></i>
                            </div>
                        </div>
                        <div v-if="selectedCartProduct" >
                            <div class="mt-3 img rounded-2xl w-[95%] m-auto h-44 bg-[#F5F5F5] flex justify-center items-center">
                                <img class="max-h-32 w-auto object-contain" :src="'http://127.0.0.1:8000/storage/' + selectedCartProduct.foto_product" alt="{{ selectedCartProduct.kode_product }}">
                            </div>
                            <div class="flex flex-col mt-1 p-3">
                                <p class="mt-2 text-gray-700 font-semibold text-lg">{{ selectedCartProduct.np }}</p>
                                <p class="text-gray-500 text-sm">{{ selectedCartProduct.deskripsi_product }}</p>
                                <textarea v-model="note" class="flex justify-start items-start mt-4 w-full h-16 bg-[#F5F5F5] text-gray-700 text-xs border-none focus:outline-none focus:ring-0 rounded-2xl p-2 text-left align-top placeholder:text-left" type="text" name="" id="" :placeholder="selectedCartProduct.note.length === 0 ? 'Add notes to your order...' : ''"></textarea>
                            </div>
                        </div>

                        <div class="flex justify-end mt-3">
                            <button @click="saveNote" class="bg-[#2D71F8] w-full text-white px-4 py-4 rounded-b-2xl hover:bg-[#6196ff]">Save Note</button>
                        </div>
                    </div>
                </div>
            </div>
            <transition name="slide">
                <div v-if="showCart" class="cart flex flex-col  w-[32.6%] h-screen bg-white shadow-2xl shadow-slate-100 z-50">
                    <div class="flex flex-row h-auto w-full px-3 pt-3 pb-3 justify-between items-center shadow-lg shadow-gray-100">
                        <div v-if="showCart" @click="toggleCart" class="cart-btn-before w-[3.2rem] h-[3.2rem] flex items-center justify-center rounded-full bg-gray-100 text-slate-700 cursor-pointer">
                            <i class="ri-file-list-3-line text-current text-xl"></i>
                        </div>
                        <div class="flex flex-col text-center items-center">
                            <div class="text-lg font-semibold text-slate-700">{{ guest }} Guest</div>
                            <div class="text-sm text-gray-400"> Order Number: </div>
                        </div>
                        <div @click="toggleCart" class="cart-btn-before w-[3.2rem] h-[3.2rem] flex items-center justify-center rounded-full bg-gray-100 text-slate-700 cursor-pointer">
                            <i class="ri-pencil-line text-current text-xl"></i>
                        </div>
                    </div>
                    <div class="flex flex-row h-auto w-full px-3 pt-2 pb-3 justify-between items-center shadow-lg shadow-gray-100">
                        <div class="flex flex-row w-[48.5%] font-[500] text-slate-800 bg-gray-100 cursor-pointer items-center justify-between rounded-full px-4 py-3">
                            <div class="text-sm w-auto">Table M-001</div>
                            <div class="icons flex items-center justify-center w-5 h-5 rounded-full bg-white">
                                <i class="ri-arrow-down-s-fill"></i>
                            </div>
                        </div>
                        <div class="flex flex-row w-[48.5%] font-[500] text-slate-800 bg-gray-100 cursor-pointer items-center justify-between rounded-full px-4 py-3">
                            <div class="text-sm w-auto">Dine In</div>
                            <div class="icons flex items-center justify-center w-5 h-5 rounded-full bg-white">
                                <i class="ri-arrow-down-s-fill"></i>
                            </div>
                        </div>
                    </div>
                    <div class="h-[50%] overflow-auto">
                        <div v-if="cart.length > 0" v-for="(item, index) in cart" :key="index" class="flex flex-row w-full h-28 px-3 border-dashed" :class="{ 'border-t-2': index !== 0 }">
                            <div class="mt-3 img rounded-xl w-[7.1rem] h-[5.2rem] bg-[#F5F5F5] flex justify-center items-center">
                                <img class="max-h-12 w-auto object-contain" :src="'http://127.0.0.1:8000/storage/' + item.foto_product">
                            </div>
                            <div  class="mid flex flex-col w-full mt-3 pl-3 h-auto items-start">
                                <div class="text-sm text-slate-800 font-medium">{{ item.np }} x {{ item.quantity }}</div>
                                <div class="text-sm text-[#2D71F8] font-medium">Rp{{ formatCurrency(item.tt_b) }}</div>
                                <div class="flex flex-row mt-2 w-full justify-between">
                                    <div class="flex flex-row">
                                        <div v-if="item.note.length > 1" class="flex items-center justify-center bg-[#bad1ff] w-9 h-9 rounded-full cursor-pointer">
                                            <div @click="openCartNoteModal(item)" class="flex items-center justify-center bg-[#2D71F8] w-7 h-7 rounded-full text-white">
                                                <i class="ri-pencil-line text-current text-sm text-current"></i>
                                            </div>
                                        </div>
                                        <div v-else @click="openCartNoteModal(item)" class="flex items-center justify-center bg-gray-100 w-9 h-9 rounded-full cursor-pointer">
                                            <div class="flex items-center justify-center bg-white w-7 h-7 rounded-full text-gray-700">
                                                <i class="ri-pencil-line text-current text-sm text-current"></i>
                                            </div>
                                        </div>
                                        <div @click="removeFromCart(index)" class="ml-2 flex items-center justify-center bg-rose-200 w-9 h-9 rounded-full cursor-pointer">
                                            <div class="flex items-center justify-center bg-[#FC4A4A] w-7 h-7 rounded-full text-white">
                                                <i class="ri-delete-bin-line text-current text-sm text-current"></i>
                                            </div>
                                        </div>

                                    </div>
                                    <div class=" quantity w-[40%] h-9 rounded-full bg-[#F5F5F5] flex flex-row justify-between items-center px-1">
                                        <div @click="decreaseQty" class="flex items-center justify-center bg-white w-7 h-7 rounded-full cursor-pointer">
                                            <i class="bi bi-dash text-xs"></i>
                                        </div>
                                        <div class="text-sm">{{ item.quantity }}</div>
                                        <div @click="increaseQty" class="flex items-center justify-center bg-white w-7 h-7 rounded-full cursor-pointer">
                                            <i class="bi bi-plus-lg text-xs"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="flex flex-row w-full items-center justify-center h-28 px-3 items border-b-2 border-dashed">
                            <div class="text-sm text-gray-400">No Item Selected</div>
                        </div>
                    </div>
                    <div class="w-full h-[40%] rounded-t-3xl shadow-[0px_-10px_20px_rgba(0,0,0,0.1)] shadow-slate-100">
                        <div class="w-full flex flex-col items-start px-3 pt-5 gap-1 pb-2 border-b-2 border-dashed">
                            <div class="flex flex-row w-full justify-between items-center">
                                <div class="text-sm text-slate-700">Subtotal</div>
                                <div class="text-sm text-slate-700">Rp {{formatCurrency(calculateSubtotal())}}</div>
                            </div>
                            <div class="flex flex-row w-full justify-between items-center">
                                <div class="text-sm text-slate-400">Tax(%)</div>
                                <div class="text-sm text-slate-400">Rp {{ formatCurrency(calculateTotalPajak())}}</div>
                            </div>
                            <div class="flex flex-row w-full justify-between items-center">
                                <div class="text-sm text-slate-400">Rounding</div>
                                <div class="text-sm text-slate-400">Rp {{ formatCurrency(calculateTotalPajak())}}</div>
                            </div>
                            <div class="flex flex-row w-full justify-between items-center">
                                <div class="text-sm font-medium text-[#1C8370]">Discount</div>
                                <div class="text-sm font-medium text-[#1C8370]">-Rp 9.000</div>
                            </div>
                        </div>
                        <div class="w-full flex-col items-start px-3 pt-1">
                            <div class="flex flex-row w-full justify-between items-center">
                                <div class="text-lg text-slate-800">Total</div>
                                <div class="text-lg text-slate-800">Rp {{formatCurrency(calculateTotal())}}</div>
                            </div>
                        </div>
                        <div class="flex flex-row h-auto w-full px-3 pt-2 pb-5 justify-between items-center">
                        <div class="flex flex-row w-[48.5%] font-[500] text-gray-400 bg-[#F6F6F6] cursor-pointer items-center justify-between rounded-full pl-4 pr-1 py-1">
                            <div class="text-sm font-normal w-auto">Add Promo</div>
                            <div class="icons flex items-center justify-center w-9 h-9 rounded-full text-slate-700 bg-white">
                                <i class="ri-discount-percent-line text-current text-xl"></i>
                            </div>
                            <!-- <div class="text-sm font-normal w-auto">Promo applied</div>
                            <div class="icons flex items-center justify-center w-9 h-9 rounded-full text-white bg-[#1C8370]">
                                <i class="ri-discount-percent-line text-current text-xl"></i>
                            </div> -->
                        </div>
                        <div class="flex flex-row w-[48.5%] font-[500] text-[#2D71F8] bg-[#f5f8ff] border border-[#2D71F8] cursor-pointer items-center justify-between rounded-full pl-4 pr-1 py-1">
                            <div class="text-sm font-normal w-auto">{{ payment }}</div>
                            <div class="icons flex items-center justify-center w-9 h-9 rounded-full text-white bg-[#2D71F8]">
                                <i class="ri-bank-card-line text-current text-xl"></i>
                            </div>
                        </div>
                        </div>
                        <button class="bg-[#2D71F8] w-full h-[3.7rem] text-white text-center px-4 py-4 hover:bg-[#6196ff]">Add to Cart</button>
                    </div>
                </div>
            </transition>
        </div>
    </template>
