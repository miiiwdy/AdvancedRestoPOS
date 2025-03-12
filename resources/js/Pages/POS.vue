    <script setup>
    import { ref, onMounted, computed, watch } from "vue";
    
    const props = defineProps({
        product: Array,
        kategori: Array,
        namaKasir: String,
        pajak: Number,
        payment: Array,
        diskonThresholdByOrder: Array,
        diskonThresholdByProduct: Array,
    })

    onMounted(() => {
        updateDateTime();
        setInterval(updateDateTime, 1000);
        typeEffect();
    });

    const allActiveDiscounts = computed(() => [
        ...(props.diskonThresholdByProduct || []), 
        ...(props.diskonThresholdByOrder || [])
    ]);

    const getDiskonThresholdByProductData = computed(() => 
        props.product.find(p => p.id === props.diskonThresholdByProduct.target_product_id) || null
    );

    const DTBP_kode_product = computed(() => getDiskonThresholdByProductData.value?.kode_product || "Tidak Ditemukan");
    const DTBP_nama_product = computed(() => getDiskonThresholdByProductData.value?.nama_product || "Tidak Ditemukan");
    const DTBP_deskripsi_product = computed(() => getDiskonThresholdByProductData.value?.deskripsi_product || "Tidak Ditemukan");
    const DTBP_foto_product = computed(() => getDiskonThresholdByProductData.value?.foto_product || "Tidak Ditemukan");
    const DTBP_kategori = computed(() => getDiskonThresholdByProductData.value?.kategoris_id || "Tidak Ditemukan");
    const DTBP_harga_product = computed(() => getDiskonThresholdByProductData.value?.harga_product || "Tidak Ditemukan");
    const DTBP_harga_beli_product = computed(() => getDiskonThresholdByProductData.value?.harga_beli_product || "Tidak Ditemukan");
    const DTBP_id_product = computed(() => getDiskonThresholdByProductData.value?.id || "Tidak Ditemukan");

    const time = ref("");
    const period = ref("");
    const date = ref("");
    const activeMenu = ref(1337);
    const searchQuery = ref('');
    const isModalOpen = ref(false);
    const isCartNoteModalOpen = ref(false);
    const isPaymentModalOpen = ref(false);
    const isAmountPaidModalOpen = ref(false);
    const isGuestEditModalOpen = ref(false);
    const isChangeModalOpen = ref(false);
    const isCheckDiscountModalOpen = ref(true);
    const selectedProduct = ref(null);
    const selectedCartProduct = ref(null);
    const quantity = ref(1);
    var note = ref('');
    const cart = ref([]);
    const showCart = ref(true);
    const orderID = ref('');
    const orderType = ref('Dine In')
    const guest = ref(0);
    const paymentData = ref('Payment');
    const placeholderText = "Search something sweet on your mind here...";
    const displayPlaceholder = ref("");
    const rounding = ref(0);
    const totalAfterRounding = ref(0);
    const amountPaid = ref(0);
    const change = ref(0);
    var isCooldown = false;
    var index = 0;
    var isDeleting = false;
    var isConfirmPayment = false;


    const toggleCart = () => {
        showCart.value = !showCart.value;
    };
    const toggleGuest = () => {
        !isGuestEditModalOpen.value ? isGuestEditModalOpen.value = true : isGuestEditModalOpen.value = false;
    }

    const storeGuest = () => {
        cart.value.forEach(item => {
                item.guest = guest.value;
            });
        toggleGuest();
    }
    const openModal = (product) => {
        selectedProduct.value = product;
        isModalOpen.value = true;
        const existingProductIndex = cart.value.findIndex(item => item.kode_product === selectedProduct.value.kode_product);
        if (existingProductIndex !== -1) {
            alert('produk sudah ada di keranjang')
            isModalOpen.value = false;
                return;
            }
    };

    const closeModal = () => {
        isModalOpen.value = false;
        selectedProduct.value = null;
        note.value = '';
        quantity.value = 1;
    };
    const openCartNoteModal = (item) => {
        if (isConfirmPayment) return;
        if (isCartNoteModalOpen.value && selectedCartProduct.value === item) {
            isCartNoteModalOpen.value = false;
            selectedCartProduct.value = null;
        } 
        else {
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

    const storeAmountPaid = () => {
        const amountPaidNumber = Number(amountPaid.value.replace(/\D/g, ""));
        const changeData = amountPaidNumber - totalRounding();
        change.value = changeData;
        
        if (totalRounding() < amountPaidNumber) {
            cart.value.forEach(item => {
                item.amount_paid = amountPaidNumber;
                item.change = changeData;
            });
            console.log('kembalian');
            amountPaid.value = "";
            isAmountPaidModalOpen.value = false;
            isChangeModalOpen.value = true;
        } 
        else if (totalRounding() === amountPaidNumber) {
            console.log('pas');
            cart.value.forEach(item => {
                item.amount_paid = 0;
            });
        } 
        else {
            console.warn('uangnya kurang');
        }
    };

    const closeChangeModal = () => {
        isChangeModalOpen.value = false;
        change.value = 0;
    }


    const increaseQty = () => {
        quantity.value++
    };

    const decreaseQty = () => {
        if (quantity.value > 1) {
            quantity.value--;
        }
    };

    const increaseExistQty = (item) => {
        if (isConfirmPayment) return;
        const cartItem = cart.value.find(cartItem => cartItem.id_product === item.id_product); 
        if (cartItem) {
            cartItem.quantity++;
            cartItem.tt_b = cartItem.hj * cartItem.quantity;
            cartItem.total_pajak = cartItem.tt_b * (props.pajak / 100);
            cartItem.tt_a = cartItem.tt_b + cartItem.total_pajak;
            console.log(cart);

            const applicableDiscounts2 = props.diskonThresholdByProduct.filter((diskon) =>
                cartItem.id_product === diskon.product_id &&
                cartItem.quantity === diskon.minimum_items_count
            );
            const isBonusExist = cart.value.some(c => c.note === props.diskonThresholdByProduct.nama_diskon);
            if (!isBonusExist) {
            applicableDiscounts2.forEach((diskon) => {
                const relatedProduct2 = props.product.find(p => p.id === diskon.target_product_id) || {};
                cart.value.unshift({
                    np: relatedProduct2.nama_product || "Tidak Ditemukan",
                    kode_product: relatedProduct2.kode_product || "Tidak Ditemukan",
                    deskripsi_product: relatedProduct2.deskripsi_product || "Tidak Ditemukan",
                    foto_product: relatedProduct2.foto_product || "Tidak Ditemukan",
                    kategori: relatedProduct2.kategoris_id || "Tidak Ditemukan",
                    quantity: diskon.target_product_quantity,
                    hb: relatedProduct2.harga_beli_product || 0,
                    thb: 0,
                    hj: relatedProduct2.harga_product || 0,
                    tt_b: 0,
                    tt_a: 0,
                    total_pajak: 0,
                    note: diskon.nama_diskon,
                    payment: paymentData.value,
                    rounding: rounding.value,
                    total_after_rounding: totalAfterRounding.value,
                    amount_paid: amountPaid.value,
                    change: change.value,
                    orderID: createorderID(),
                    guest: guest.value || '',
                    orderType: orderType.value,
                    id_product: relatedProduct2.id || "Tidak Ditemukan",
                });
            });
            console.log("Diskon yang diterapkan:", applicableDiscounts2);
            }
        }
    };


    const decreaseExistQty = (item) => {
        if (isConfirmPayment) return;
        const cartItem = cart.value.find(cartItem => cartItem.id_product === item.id_product);
        if (cartItem && cartItem.quantity > 1) {
            cartItem.quantity--;
            cartItem.tt_b = cartItem.hj * cartItem.quantity;
            cartItem.total_pajak = cartItem.tt_b * (props.pajak / 100);
            cartItem.tt_a = cartItem.tt_b + cartItem.total_pajak;
            const applicableDiscounts3 = props.diskonThresholdByProduct.filter(
                (diskon) => cartItem.id_product === diskon.product_id && cartItem.quantity < diskon.minimum_items_count
            );
            if (applicableDiscounts3.length > 0) {
                for (let i = cart.value.length - 1; i >= 0; i--) {
                    if (cart.value[i].note && applicableDiscounts3.some(d => cart.value[i].note === d.nama_diskon)) {
                        cart.value.splice(i, 1);
                    }
                }
                console.log(`product diskon diaps`);
            } else {
                console.log(`gaada produk diskon yg diapus`);
            }
        }
    };


    const closePaymentModal = () => {
        isPaymentModalOpen.value = false;
    };
    const openPaymentModal = () => {
        isPaymentModalOpen.value = true;
    };
    const confirmPayment = (selectedPayment) => {
        isConfirmPayment = true;
        if (!selectedPayment || !selectedPayment.payment_name) {
            console.warn("Payment method not selected 1");
            isPaymentModalOpen.value = false;
            return;
        }
        if (cart.value.length === 0) {
            console.warn("cart lo kosong");
            isPaymentModalOpen.value = false;
            return;
        }
        else {
            paymentData.value = selectedPayment.payment_name;
            cart.value.forEach(item => {
                item.payment = paymentData.value;
            });
            isPaymentModalOpen.value = false;
            isAmountPaidModalOpen.value = true;
        }
    };

    const calculateSubtotal = () => cart.value.reduce((sum, item) => sum + item.tt_b, 0);
    const calculateTotalPajak = () => cart.value.reduce((sum, item) => sum + item.total_pajak, 0);
    const calculateTotal = () => cart.value.reduce((sum, item) => sum + item.tt_a, 0);
    
    const totalRounding = () => {
        totalAfterRounding.value = calculateTotal() > 0 ? Math.round(calculateTotal() / 500) * 500 : 0;
        cart.value.forEach(item => {
            item.total_after_rounding = totalAfterRounding.value;
        });
        return calculateTotal() > 0 ? Math.round(calculateTotal() / 500) * 500 : 0;
    }
    const roundingAmount = () => {
        const total = calculateTotal();
        const roundedTotal = totalRounding();
        rounding.value = roundedTotal - total;

        console.log("calculateTotal:", total);
        console.log("totalRounding:", roundedTotal);
        cart.value.forEach(item => {
            item.rounding = rounding.value;
        });
        return roundedTotal - total;
    }
    
    function createorderID() {
        if (!orderID.value) {
            const hurup = Math.random().toString(36).replace(/[^a-z]/g, '').substring(0, 3).toUpperCase();
            const angka = Math.floor(Math.random() * 900) + 100;
            const ON = hurup + angka;
            orderID.value = ON;
            return ON
        }
        else {
            return orderID.value;
        }
    }

    const getOrderType = () => {
        orderType.value = orderType.value === 'Dine In' ? 'Take Away' : 'Dine In';
    }

    const addToCart = () => {
        if (selectedProduct.value) {
            const totalHargaBeliPerItem = selectedProduct.value.harga_beli_product;
            const totalHargaBeli = totalHargaBeliPerItem * quantity.value;
            const totalHargaPerItem = selectedProduct.value.harga_product;
            const totalHargaBefore = totalHargaPerItem * quantity.value;
            const existingProductIndex = cart.value.findIndex(item => item.kode_product === selectedProduct.value.kode_product);
            const totalPajakPerItem =  totalHargaBefore * (props.pajak / 100);
            const totalHargaAfter = totalHargaBefore + totalPajakPerItem;

            if (existingProductIndex !== -1) {
                alert('produk sudah ada di keranjang')
                return;
            }

            // if (selectedProduct.value.id === props.diskonThresholdByProduct.product_id && quantity.value >= props.diskonThresholdByProduct.minimum_items_count) {
            //     // const totalHargaBeliDTBP = DTBP_harga_beli_product.value * props.diskonThresholdByProduct.target_product_quantity;
            //     // const totalHargaJualDTBP = DTBP_harga_product.value * props.diskonThresholdByProduct.target_product_quantity;
            //     // const totalPajakDTBP = totalHargaJualDTBP * (props.pajak / 100);
            //     // const totalHargaAfterDTBP = totalHargaJualDTBP + totalPajakDTBP;
            //     cart.value.unshift({
            //         np: DTBP_nama_product.value,
            //         kode_product: DTBP_kode_product.value,
            //         deskripsi_product: DTBP_deskripsi_product.value,
            //         foto_product: DTBP_foto_product.value,
            //         kategori: DTBP_kategori.value,
            //         quantity: props.diskonThresholdByProduct.target_product_quantity,
            //         hb: 0,
            //         thb: 0,
            //         hj: 0,
            //         tt_b: 0,
            //         tt_a: 0,
            //         total_pajak: 0,
            //         note: props.diskonThresholdByProduct.nama_diskon,
            //         payment: paymentData.value,
            //         rounding: rounding.value,
            //         total_after_rounding: totalAfterRounding.value,
            //         amount_paid: amountPaid.value,
            //         change: change.value,
            //         orderID: createorderID(),
            //         guest: guest.value || '',
            //         orderType: orderType.value,
            //         id_product: DTBP_id_product,
            //     })
            //     console.log('Nambah barang baru cuy, Diskon terpakai!');
                
            // }
            const applicableDiscounts = props.diskonThresholdByProduct.filter(
                (diskon) =>
                    selectedProduct.value.id === diskon.product_id &&
                    quantity.value >= diskon.minimum_items_count
            );

            applicableDiscounts.forEach((diskon) => {
                const relatedProduct = props.product.find(p => p.id === diskon.target_product_id) || {};
                cart.value.unshift({
                    np: relatedProduct.nama_product || "Tidak Ditemukan",
                    kode_product: relatedProduct.kode_product || "Tidak Ditemukan",
                    deskripsi_product: relatedProduct.deskripsi_product || "Tidak Ditemukan",
                    foto_product: relatedProduct.foto_product || "Tidak Ditemukan",
                    kategori: relatedProduct.kategoris_id || "Tidak Ditemukan",
                    quantity: diskon.target_product_quantity,
                    hb: relatedProduct.harga_beli_product || 0,
                    thb: 0,
                    hj: relatedProduct.harga_product || 0,
                    tt_b: 0,
                    tt_a: 0,
                    total_pajak: 0,
                    note: diskon.nama_diskon,
                    payment: paymentData.value,
                    rounding: rounding.value,
                    total_after_rounding: totalAfterRounding.value,
                    amount_paid: amountPaid.value,
                    change: change.value,
                    orderID: createorderID(),
                    guest: guest.value || '',
                    orderType: orderType.value,
                    id_product: relatedProduct.id || "Tidak Ditemukan",
                });
            });
            console.log("Diskon yang diterapkan:", applicableDiscounts);

            cart.value.unshift({
                np: selectedProduct.value.nama_product,
                kode_product: selectedProduct.value.kode_product,
                deskripsi_product: selectedProduct.value.deskripsi_product,
                foto_product: selectedProduct.value.foto_product,
                kategori: selectedProduct.value.kategoris_id,
                quantity: quantity.value,
                hb: selectedProduct.value.harga_beli_product,
                thb: totalHargaBeli,
                hj: totalHargaPerItem,
                tt_b: totalHargaBefore,
                tt_a: totalHargaAfter,
                total_pajak: totalPajakPerItem,
                note: note.value || '',
                payment: paymentData.value,
                rounding: rounding.value,
                total_after_rounding: totalAfterRounding.value,
                amount_paid: amountPaid.value,
                change: change.value,
                orderID: createorderID(),
                guest: guest.value || '',
                orderType: orderType.value,
                id_product: selectedProduct.value.id,
            })
            console.log(cart);
            console.log(cart.value.length);
            isModalOpen.value = false;
        }
    }

    const confirmOrder = () => {
        if (paymentData.value === "Payment" || !paymentData.value) {
            console.log('adawd');
            return;
        }
        if (cart.value.length === 0) {
            console.log('Keranjang kosong. Silakan tambahkan produk.');
            return;
        }
    }

    const removeFromCart = (index) => {
        if (isCooldown) {      
            console.log('cooldown');
            return;
        }
        if (isConfirmPayment) return;

        //kayanya ga perlu
        // if (cart.value.length === 1) {
        //     orderID.value === ''
        // }
        if (cart.value.length > 0) {
            isCooldown = true;
            const removedItem = cart.value.splice(index, 1)[0];
            const totalHargaSebelumDiskonPajak = cart.value.reduce((total, item) => {
                return total + item.tt_b;
            }, 0);

            console.log('product dihapus:', removedItem);
            console.log('Total harga setelah penghapusan:', totalHargaSebelumDiskonPajak);

            setTimeout(() => {
                isCooldown = false;
                console.log('Cooldown selesai, Anda dapat menghapus product lagi.');
            }, 1000);
        } else {
            console.log('Keranjang kosong, tidak ada yang bisa dihapus.');
        }
    };

    const filteredAndSortedProducts = computed(() => {
        if (!Array.isArray(props.product)) return [];

        const query = searchQuery.value.toLowerCase();
        const filtered = props.product.filter(product => {
            const matchesSearch = product.nama_product.toLowerCase().includes(query);
            if (activeMenu.value === 1337) {
                return matchesSearch;
            }
            const matchesCategory = activeMenu.value ? product.kategoris_id === activeMenu.value : true;
            return matchesSearch && matchesCategory;
        });
        return filtered;
    });

    const isProductAvailable = computed(() => {
        return searchQuery.value === '' || filteredAndSortedProducts.value.length > 0;
    });

    const toggleActive = (categoryId) => {
        activeMenu.value = categoryId;
    };
    const formatCurrency = (value) => {
        if (!value) return "0";
        return Number(value).toLocaleString('id-ID');
    };
    const inputFormatCurrency = (event) => {
        let value = event.target.value.replace(/\D/g, "");
        value = Number(value).toLocaleString("id-ID");
        amountPaid.value = value;
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
        <div class="flex flex-row h-screen w-full bg-[#F8F8F8] tracking-tight overflow-y-hidden no-select">
            <!-- kiri -->
            <div class="flex flex-col h-screen" :class="showCart ? 'w-[76%]' : 'w-full'">
                <div class="flex flex-row w-full px-4 py-4 pb-1 h-auto items-center gap-4 justify-between">
                    <div class="flex gap-4">
                        <div class="hamburger-menu w-[3.2rem] h-[3.2rem] flex items-center justify-center rounded-full bg-white text-[#2D71F8] cursor-pointer">
                            <i class="ri-menu-5-fill text-current text-xl"></i>
                        </div>
                        <div class="flex w-auto pr-5 h-[3.2rem] rounded-full items-center bg-white">
                            <div class="w-9 h-9 rounded-full mx-2 flex items-center justify-center bg-[#f0f7ff]">
                                <img class="h-6 w-auto" src="../Assets/profileimg.png" alt="">
                            </div>
                            <div class="flex flex-col justify-center mb-1">
                                <div class="font-medium text-gray-700">
                                    {{ props.namaKasir }}
                                </div>
                                <div class="text-xs font-normal text-gray-400">
                                    Cashier
                                </div>
                            </div>
                        </div>
                        <div class="tanggal w-60 h-[3.2rem] rounded-full items-center bg-white flex">
                            <div class="wrap px-2 flex flex-row items-center">
                                <div
                                class="icon w-9 h-9 bg-[#f0f7ff] rounded-full flex items-center justify-center text-[#2D71F8]">
                                <i class="ri-calendar-line text-current text-lg"></i>
                            </div>
                            <p class="text-gray-700 font-medium ml-3">{{ date }}</p>
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
                            <div class="total_product_in_kategori text-sm text-gray-500">{{ props.product.length }} items</div>
                        </div>
                    </div>  
                    <div v-for="kategori in kategori" :key="kategori.id" @click="toggleActive(kategori.id)" :class="['flex flex-row py-3 px-3 w-48 h-20 rounded-2xl cursor-pointer',activeMenu === kategori.id ? 'bg-[#f0f7ff] outline outline-2 outline-[#2D71F8]' : 'bg-white']">
                        <div :class="['category_icon flex justify-center items-center w-14 h-14 rounded-full',activeMenu === kategori.id ? 'bg-[#2D71F8] text-white' : 'bg-gray-100 text-gray-500']">
                            <i class="text-xl text-current" :class="[kategori.icon]"></i>
                        </div>
                        <div class="wrap flex flex-col ml-3 justify-center">
                            <div class="nama_kategori font-semibold text-lg text-gray-700">{{ kategori.nama_kategori}}</div>
                            <div class="total_product_in_kategori text-sm text-gray-500">{{ getProductCount(kategori.id) }} items</div>
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
                    <div v-if="!isProductAvailable" class="flex w-full h-64 rounded-xl overflow-hidden justify-center">
                        <h2 class="text-xl text-gray-400">No Product Available</h2>
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
                                <img class="max-h-32 w-auto object-contain" :src="'http://127.0.0.1:8000/storage/' + selectedProduct.foto_product" alt="{{ selectedProduct.kode_product }} {{ selectedProduct.deskripsi_product }} {{ selectedProduct.id }} {{ selectedProduct.harga_beli_product }}">
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
                <!-- Payment Modal -->
                <div v-if="isPaymentModalOpen" class="fixed inset-0 flex items-center justify-center bg-slate-400 bg-opacity-50" @click.self="closePaymentModal">
                    <div class="absolute top-1/2 left-1/3 transform -translate-x-[7rem] -translate-y-1/2 bg-white rounded-2xl w-[26rem] shadow-2xl">
                        <div class="flex flex-row justify-between items-center justify-center shadow-lg shadow-gray-100 rounded-md p-3 pb-3">
                            <div class="flex w-9 h-9 bg-white"></div>
                            <h2 class="text-normal font-normal">Payment</h2>
                            <div class="flex items-center justify-center rounded-full bg-[#fff2f3] w-9 h-9 text-[#FC4A4A] cursor-pointer" @click="closePaymentModal">
                                <i class="bi bi-x-lg text-current"></i>
                            </div>
                        </div>
                        <div class="flex w-full flex-col px-3 max-h-96 overflow-auto mb-5">
                            <div v-for="payment in payment" :key="payment.id">
                                <div @click="confirmPayment(payment)" class="cursor-pointer flex items-center justify-center w-full h-16 mt-4 bg-gray-100 rounded-2xl font-semibold">{{ payment.payment_name }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Amount Paid Modal -->
                <div v-if="isAmountPaidModalOpen" class="fixed inset-0 flex items-center justify-center bg-slate-400 bg-opacity-50">
                    <div class="absolute top-1/2 left-1/3 transform -translate-x-[7rem] -translate-y-1/2 bg-white rounded-2xl w-[26rem] shadow-2xl">
                        <div class="flex flex-row justify-center items-center justify-center shadow-lg shadow-gray-100 rounded-md p-3 pb-3">
                            <h2 class="text-normal h-9 flex items-center justify-center font-normal">Amount Paid</h2>
                        </div>
                        <input v-model="amountPaid" @input='inputFormatCurrency' class="flex justify-center items-center text-center mt-4 w-[85%] mx-auto h-16 bg-[#F5F5F5] text-gray-700 text-md border-none focus:outline-none focus:ring-0 rounded-2xl py-2 appearance-none" type="text" name="" id="" placeholder="Input Customer Amount Paid">
                        <div class="flex justify-center items-center mt-3">
                            <button @click="storeAmountPaid" class="bg-[#2D71F8] w-full text-white px-4 py-4 rounded-b-2xl hover:bg-[#6196ff]">Confirm</button>
                        </div>
                    </div>
                </div>
                <!-- Kembalien Modal -->
                <div v-if="isChangeModalOpen" class="fixed inset-0 flex items-center justify-center bg-slate-400 bg-opacity-50">
                    <div class="absolute top-1/2 left-1/3 transform -translate-x-[7rem] -translate-y-1/2 bg-white rounded-2xl w-[26rem] shadow-2xl">
                        <div class="flex flex-row justify-center items-center justify-center shadow-lg shadow-gray-100 rounded-md p-3 pb-3">
                            <h2 class="text-normal font-normal">Change</h2>
                        </div>
                        <div class="flex justify-center items-center mt-4 w-[85%] mx-auto h-16 bg-[#F5F5F5] text-gray-700 text-md rounded-2xl">
                            Rp {{ formatCurrency(change) }}
                        </div>
                        <div class="flex justify-center items-center mt-3">
                            <button @click="closeChangeModal" class="bg-[#2D71F8] w-full text-white px-4 py-4 rounded-b-2xl hover:bg-[#6196ff]">Confirm</button>
                        </div>
                    </div>
                </div>
                <!-- guest edit modal -->
                <div v-if="isGuestEditModalOpen" class="fixed inset-0 flex items-center justify-center bg-slate-400 bg-opacity-50" @click.self(isGuestEditModalOpen)>
                    <div class="absolute top-1/2 left-1/3 transform -translate-x-[7rem] -translate-y-1/2 bg-white rounded-2xl w-[26rem] shadow-2xl">
                        <div class="flex flex-row justify-center items-center justify-center shadow-lg shadow-gray-100 rounded-md p-3 pb-3">
                            <h2 class="text-normal h-9 flex items-center justify-center font-normal">Edit Guest</h2>
                        </div>
                        <input v-if="guest.length > 0" v-model="guest"class="flex justify-center items-center text-center mt-4 w-[85%] mx-auto h-16 bg-[#F5F5F5] text-gray-700 text-md border-none focus:outline-none focus:ring-0 rounded-2xl py-2 appearance-none" type="text" name="" id="" placeholder="JANCUK">
                        <input v-else @input="guest = $event.target.value"  class="flex justify-center items-center text-center mt-4 w-[85%] mx-auto h-16 bg-[#F5F5F5] text-gray-700 text-md border-none focus:outline-none focus:ring-0 rounded-2xl py-2 appearance-none" type="text" name="" id="" placeholder="Input Guest Count">
                        <div class="flex justify-center items-center mt-3">
                            <button @click="storeGuest" class="bg-[#2D71F8] w-full text-white px-4 py-4 rounded-b-2xl hover:bg-[#6196ff]">Confirm</button>
                        </div>
                    </div>
                </div>
                <!-- Check Discount Modal -->
                <div v-if="isCheckDiscountModalOpen" class="fixed inset-0 flex items-center justify-center bg-slate-400 bg-opacity-50" @click.self="closePaymentModal">
                    <div class="absolute top-1/2 left-1/3 transform -translate-x-[13rem] -translate-y-1/2 bg-white rounded-2xl w-[35rem] shadow-2xl">
                        <div class="flex flex-row justify-between items-center justify-center shadow-lg shadow-gray-100 rounded-md p-3 pb-3">
                            <div class="flex w-9 h-9 bg-white"></div>
                            <h2 class="text-normal font-normal">Check Discount</h2>
                            <div class="flex items-center justify-center rounded-full bg-[#fff2f3] w-9 h-9 text-[#FC4A4A] cursor-pointer" @click="closePaymentModal">
                                <i class="bi bi-x-lg text-current"></i>
                            </div>
                        </div>
                        <div class="flex w-full flex-col px-3 max-h-96 overflow-auto mb-5">
                            <div v-for="diskon in allActiveDiscounts" :key="diskon.id">
                                <div class="cursor-pointer flex items-center justify-center w-full h-16 mt-4 bg-gray-100 rounded-2xl font-semibold">{{ payment.payment_name }}</div>
                            </div>
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
                            <div class="text-sm text-gray-400"> Order ID: {{ cart.length > 0 ? orderID : ''}}</div>
                        </div>
                        <div @click="toggleGuest" class="cart-btn-before w-[3.2rem] h-[3.2rem] flex items-center justify-center rounded-full bg-gray-100 text-slate-700 cursor-pointer">
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
                        <div @click=(getOrderType) class="flex flex-row w-[48.5%] font-[500] text-slate-800 bg-gray-100 cursor-pointer items-center justify-between rounded-full px-4 py-3">
                            <div class="text-sm w-auto">{{ orderType }}</div>
                            <div class="icons flex items-center justify-center w-5 h-5 rounded-full bg-white">
                                <i class="ri-loop-right-fill"></i>
                            </div>
                        </div>
                    </div>
                    <div class="h-full overflow-auto">
                        <div v-if="cart.length > 0" v-for="(item, index) in cart" :key="index" class="flex flex-row w-full h-28 px-3 border-dashed" :class="{ 'border-t-2': index !== 0, 'relative before:block before:content-[\' \'] before:absolute before:top-0 before:right-0 before:bottom-0 before:w-40 before:bg-gradient-to-l before:from-green-500/30 before:to-transparent shadow-inner': props.diskonThresholdByProduct.some(diskon => item.note === diskon.nama_diskon)}">
                        <div class="mt-3 img rounded-xl w-[7.1rem] h-[5.2rem] bg-[#F5F5F5] flex justify-center items-center">
                            <img class="max-h-12 w-auto object-contain" :src="'http://127.0.0.1:8000/storage/' + item.foto_product">
                        </div>
                        <div class="mid flex flex-col w-full mt-3 pl-3 h-auto items-start">
                            <div class="text-sm text-slate-800 font-medium">{{ item.np }} x {{ item.quantity }}</div>
                            <div v-for="diskon in props.diskonThresholdByProduct" :key="diskon.nama_diskon">
                                <div v-if="item.note === diskon.nama_diskon" class="text-sm text-[#1C8370] font-medium">
                                    {{ diskon.nama_diskon }}
                                </div>
                            </div>

                            <div v-if="!props.diskonThresholdByProduct.some(d => d.nama_diskon === item.note)" class="text-sm text-[#2D71F8] font-medium">
                                Rp{{ formatCurrency(item.tt_b) }}
                            </div>
                            <div class="flex flex-row mt-2 w-full justify-between">
                                <div class="flex flex-row">
                                    <div v-for="diskon in props.diskonThresholdByProduct" :key="diskon.nama_diskon">
                                        <div v-if="item.note === diskon.nama_diskon"class="flex items-center justify-center bg-[#d4ffea] w-9 h-9 rounded-full">
                                            <div class="flex items-center justify-center bg-[#1C8370] w-7 h-7 rounded-full text-white">
                                                <i class="ri-discount-percent-line text-sm text-current flex items-center justify-center"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="item.note.length > 0 && !props.diskonThresholdByProduct.some(diskon => item.note === diskon.nama_diskon)"class="flex items-center justify-center bg-[#bad1ff] w-9 h-9 rounded-full cursor-pointer">
                                        <div @click="openCartNoteModal(item)" class="flex items-center justify-center bg-[#2D71F8] w-7 h-7 rounded-full text-white">
                                            <i class="ri-pencil-line text-current text-sm text-current"></i>
                                        </div>
                                    </div>
                                    <div v-else-if="!props.diskonThresholdByProduct.some(diskon => item.note === diskon.nama_diskon)" @click="openCartNoteModal(item)"class="flex items-center justify-center bg-gray-100 w-9 h-9 rounded-full cursor-pointer">
                                        <div class="flex items-center justify-center bg-white w-7 h-7 rounded-full text-gray-700">
                                            <i class="ri-pencil-line text-current text-sm text-current"></i>
                                        </div>
                                    </div>
                                    <div v-if="!props.diskonThresholdByProduct.some(diskon => item.note === diskon.nama_diskon)" @click="removeFromCart(index)"class="ml-2 flex items-center justify-center bg-rose-200 w-9 h-9 rounded-full cursor-pointer">
                                        <div class="flex items-center justify-center bg-[#FC4A4A] w-7 h-7 rounded-full text-white">
                                            <i class="ri-delete-bin-line text-current text-sm text-current"></i>
                                        </div>
                                    </div>
                                    <div v-if="props.diskonThresholdByProduct.some(diskon => item.note === diskon.nama_diskon)" class="ml-2 quantity w-9 h-9 rounded-full bg-[#F5F5F5] flex flex-row justify-center items-center px-1">
                                        <div class="text-sm flex items-center justify-center bg-white w-7 h-7 rounded-full">
                                            {{ item.quantity }}
                                        </div>
                                    </div>
                                </div>
                                <div v-if="!props.diskonThresholdByProduct.some(diskon => item.note === diskon.nama_diskon)"
                                    class="quantity w-[40%] h-9 rounded-full bg-[#F5F5F5] flex flex-row justify-between items-center px-1">
                                    <div @click="decreaseExistQty(item)"
                                        class="flex items-center justify-center bg-white w-7 h-7 rounded-full cursor-pointer">
                                        <i class="bi bi-dash text-xs"></i>
                                    </div>
                                    <div class="text-sm">{{ item.quantity }}</div>
                                    <div @click="increaseExistQty(item)"
                                        class="flex items-center justify-center bg-white w-7 h-7 rounded-full cursor-pointer">
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
                    
                    <div class="w-full h-auto rounded-t-3xl shadow-[0px_-10px_20px_rgba(0,0,0,0.1)] shadow-slate-100">
                        <div class="w-full flex flex-col items-start px-3 pt-5 gap-1 pb-2 border-b-2 border-dashed">
                            <div class="flex flex-row w-full justify-between items-center">
                                <div class="text-sm text-slate-700">Subtotal</div>
                                <div class="text-sm text-slate-700">Rp {{formatCurrency(calculateSubtotal())}}</div>
                            </div>
                            <div class="flex flex-row w-full justify-between items-center">
                                <div class="text-sm text-slate-400">Tax ({{ props.pajak }}%)</div>
                                <div class="text-sm text-slate-400">Rp {{ formatCurrency(calculateTotalPajak())}}</div>
                            </div>
                            <div class="flex flex-row w-full justify-between items-center">
                                <div class="text-sm text-slate-400">Rounding</div>
                                <div class="text-sm text-slate-400">{{ roundingAmount() }}</div>
                            </div>
                            <div class="flex flex-row w-full justify-between items-center">
                                <div class="text-sm font-medium text-[#1C8370]">Discount</div>
                                <div class="text-sm font-medium text-[#1C8370]">-Rp 0</div>
                            </div>
                        </div>
                        <div class="w-full flex-col items-start px-3 pb-2 pt-1 shadow-sm shadow-gray-100">
                            <div class="flex flex-row w-full justify-between items-center">
                                <div class="text-lg text-slate-800">Total</div>
                                <div class="text-lg text-slate-800">Rp {{formatCurrency(totalRounding())}}</div>
                            </div>
                        </div>
                        <div class="flex flex-row h-auto w-full px-3 pt-3 pb-3 justify-between items-center">
                        <div class="flex flex-row w-[48.5%] font-[500] text-gray-400 bg-[#F6F6F6] cursor-pointer items-center justify-between rounded-full pl-4 pr-1 py-1">
                            <div class="text-sm font-normal w-auto">Check Discount</div>
                            <div class="icons flex items-center justify-center w-8 h-8 rounded-full text-slate-700 bg-white">
                                <i class="ri-discount-percent-line text-current text-xl"></i>
                            </div>
                        </div>
                        <!-- <div class="flex flex-row w-[48.5%] font-[500] text-[#1C8370] border border-[#1C8370] bg-[#f7fffc] cursor-pointer items-center justify-between rounded-full pl-4 pr-1 py-1">
                            <div class="text-sm font-normal w-auto pr-1">Discount applied</div>
                            <div class="icons flex items-center justify-center w-8 h-8 rounded-full text-white bg-[#1C8370]">
                                <i class="ri-discount-percent-line text-current text-xl"></i>
                            </div>
                        </div> -->
                        <div @click="(openPaymentModal)" v-if="!isAmountPaidModalOpen" class="flex flex-row w-[48.5%] font-[500] text-black bg-white border border-slate-700 cursor-pointer items-center justify-between rounded-full pl-4 pr-1 py-1">
                            <div class="text-sm font-normal w-auto">{{ paymentData }}</div>
                            <div class="icons flex items-center justify-center w-8 h-8 rounded-full text-slate-700 bg-[#F6F6F6]">
                                <i class="ri-bank-card-line text-current text-xl"></i>
                            </div>
                        </div>
                        <div v-if="isAmountPaidModalOpen" class="flex flex-row w-[48.5%] font-[500] text-[#2D71F8] bg-[#f5f8ff] border border-[#2D71F8] cursor-pointer items-center justify-between rounded-full pl-4 pr-1 py-1">
                            <div class="text-sm font-normal w-auto">{{ paymentData }}</div>
                            <div class="icons flex items-center justify-center w-8 h-8 rounded-full text-white bg-[#2D71F8]">
                                <i class="ri-bank-card-line text-current text-xl"></i>
                            </div>
                        </div>
                        </div>
                        <button @click="(confirmOrder)" class="bg-[#2D71F8] w-full h-[3.7rem] text-white text-center px-4 py-4 hover:bg-[#6196ff]">Confirm Order</button>
                    </div>
                </div>
            </transition>
        </div>
    </template>
