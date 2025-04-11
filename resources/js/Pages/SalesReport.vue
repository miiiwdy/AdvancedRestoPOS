<script setup>
import { ref, onMounted, computed } from "vue";

const props = defineProps({
    product: Array,
    kategori: Array,
    NamaKasir: String,
    dataOrder: Array,
    dataOrderProduct: Array,
    namaResto: String,
    namaOutlet: String,
});

onMounted(() => {
    updateDateTime();
    setInterval(updateDateTime, 1000);
    console.log("Order Data Received:", props.orderData);
  console.log("Is Array?", Array.isArray(props.orderData));
  if (Array.isArray(props.orderData)) {
    console.log("First Item:", props.orderData[0]);
  }
});

const date = ref("");
const time = ref("");
const period = ref("");
const thisMonth = ref(true);
const threeMonth = ref(false);
const sixMonth = ref(false);
const oneYear = ref(false);
const now = new Date();
const p_salesAmount = ref(false);
const p_productSales = ref(false);
const p_totalGuests = ref(false);
const p_netProfits = ref(false);
const searchQuery = ref('');
const selectedOrder = ref(null);
const isOrderDetailModalOpen = ref(false);

const openOrderDetailModal = (order) => {
        selectedOrder.value = order;
        isOrderDetailModalOpen.value = true;
    };

const closeOrderDetailModal = () => {
        isOrderDetailModalOpen.value = false;
        selectedOrder.value = null;
    };
const selectedProducts = computed(() => {
        if (!selectedOrder.value) return [];
            return props.dataOrderProduct.filter((product) => product.order_id === selectedOrder.value.order_id
        );
});

const getCurrentRange = () => {
  let start = new Date(now);
  let end = new Date(now);
  if (thisMonth.value) {
    start = new Date(now.getFullYear(), now.getMonth(), 1);
    end = new Date(now);
  } else if (threeMonth.value) {
    start = new Date(now);
    start.setMonth(now.getMonth() - 2);
    start.setDate(1);
    end = new Date(now);
  } else if (sixMonth.value) {
    start = new Date(now);
    start.setMonth(now.getMonth() - 5);
    start.setDate(1);
    end = new Date(now);
  } else if (oneYear.value) {
    start = new Date(now);
    start.setMonth(now.getMonth() - 11);
    start.setDate(1);
    end = new Date(now);
  }
  
  return { start, end };
};

const filteredDataOrder = computed(() => {
  const { start, end } = getCurrentRange();
  
  return props.dataOrder.filter(item => {
    if (!item.created_at) return false;
    const itemDate = new Date(item.created_at);
    if (isNaN(itemDate.getTime())) return false;
    return itemDate >= start && itemDate <= end;
  });
});

const filteredDataOrderProduct = computed(() => {
  const { start, end } = getCurrentRange();
  
  return props.dataOrderProduct.filter(item => {
    if (!item.created_at) return false;
    const itemDate = new Date(item.created_at);
    if (isNaN(itemDate.getTime())) return false;
    return itemDate >= start && itemDate <= end;
  });
});

const totalSalesAmount = computed(() => {
  if (filteredDataOrder.value.length === 0) return 0;
  return filteredDataOrder.value.reduce((total, item) => {
    const nilai = Number(item.total_harga_after_all || 0);
    return total + (isNaN(nilai) ? 0 : nilai);
  }, 0);
});

const netProfits = computed(() => {
  if (filteredDataOrder.value.length === 0) return 0;
  return filteredDataOrder.value.reduce((total, item) => {
    const nilai = Number(item.keuntungan || 0);
    return total + (isNaN(nilai) ? 0 : nilai);
  }, 0);
});

const totalProductSales = computed(() => {
  if (filteredDataOrderProduct.value.length === 0) return 0;
  return filteredDataOrderProduct.value.reduce((total, item) => {
    const nilai = Number(item.quantity || 0);
    return total + (isNaN(nilai) ? 0 : nilai);
  }, 0);
});

const totalGuests = computed(() => {
  if (filteredDataOrder.value.length === 0) return 0;
  return filteredDataOrder.value.reduce((total, item) => {
    const nilai = Number(item.guest || 0);
    return total + (isNaN(nilai) ? 0 : nilai);
  }, 0);
});

const enableThisMonthSorting = () => {
    if (thisMonth.value === true) {
        return;
    }
    thisMonth.value = true;
    threeMonth.value = false;
    sixMonth.value = false;
    oneYear.value = false;
};

const enableThreeMonthSorting = () => {
    if (threeMonth.value === true) {
        return;
    }
    threeMonth.value = true;
    thisMonth.value = false;
    sixMonth.value = false;
    oneYear.value = false;
};

const enableSixMonthSorting = () => {
    if (sixMonth.value === true) {
        return;
    }
    sixMonth.value = true;
    thisMonth.value = false;
    threeMonth.value = false;
    oneYear.value = false;
};

const enableOneYearSorting = () => {
    if (oneYear.value === true) {
        return;
    }
    oneYear.value = true;
    thisMonth.value = false;
    threeMonth.value = false;
    sixMonth.value = false;
};

const getPreviousRange = () => {
  const { start } = getCurrentRange();
  const prevEnd = new Date(start);
  prevEnd.setDate(prevEnd.getDate() - 1);
  const prevStart = new Date(prevEnd);
  if (thisMonth.value) {
    prevStart.setDate(1);
  } else if (threeMonth.value) {
    prevStart.setMonth(prevStart.getMonth() - 2);
    prevStart.setDate(1);
  } else if (sixMonth.value) {
    prevStart.setMonth(prevStart.getMonth() - 5);
    prevStart.setDate(1);
  } else if (oneYear.value) {
    prevStart.setMonth(prevStart.getMonth() - 11);
    prevStart.setDate(1);
  }
  return { start: prevStart, end: prevEnd };
};

const filteredPreviousDataOrder = computed(() => {
  const { start, end } = getPreviousRange();

  return props.dataOrder.filter(item => {
    if (!item.created_at) return false;
    const itemDate = new Date(item.created_at);
    if (isNaN(itemDate.getTime())) return false;
    
    return itemDate >= start && itemDate <= end;
  });
});

const filteredPreviousDataOrderProduct = computed(() => {
  const { start, end } = getPreviousRange();

  return props.dataOrderProduct.filter(item => {
    if (!item.created_at) return false;
    const itemDate = new Date(item.created_at);
    if (isNaN(itemDate.getTime())) return false;
    
    return itemDate >= start && itemDate <= end;
  });
});

const previousTotalSales = computed(() => {
  if (filteredPreviousDataOrder.value.length === 0) return 0;
  return filteredPreviousDataOrder.value.reduce((total, item) => {
    const nilai = Number(item.total_harga_after_all || 0);
    return total + (isNaN(nilai) ? 0 : nilai);
  }, 0);
});

const previousNetProfits = computed(() => {
  if (filteredPreviousDataOrder.value.length === 0) return 0;
  return filteredPreviousDataOrder.value.reduce((total, item) => {
    const nilai = Number(item.keuntungan || 0);
    return total + (isNaN(nilai) ? 0 : nilai);
  }, 0);
});

const previousTotalProducts = computed(() => {
  if (filteredPreviousDataOrderProduct.value.length === 0) return 0;
  return filteredPreviousDataOrderProduct.value.reduce((total, item) => {
    const nilai = Number(item.quantity || 0);
    return total + (isNaN(nilai) ? 0 : nilai);
  }, 0);
});

const previousGuests = computed(() => {
  if (filteredPreviousDataOrder.value.length === 0) return 0;
  return filteredPreviousDataOrder.value.reduce((total, item) => {
    const nilai = Number(item.guest || 0);
    return total + (isNaN(nilai) ? 0 : nilai);
  }, 0);
});
const getPercentageChangeWithFlag = (current, previous, flagRef) => {
  if (current === 0 && previous === 0) {
    flagRef.value = false;
    return "0.00";
  }
  
  if (previous === 0) {
    flagRef.value = false;
    return "100.00";
  }
  
  const percentChange = ((current - previous) / Math.abs(previous)) * 100;
  flagRef.value = current < previous;
  return Math.abs(percentChange).toFixed(2);
};
const salesDifference = computed(() => {
  return Math.abs(totalSalesAmount.value - previousTotalSales.value);
});

const profitDifference = computed(() => {
  return Math.abs(netProfits.value - previousNetProfits.value);
});

const productDifference = computed(() => {
  return Math.abs(totalProductSales.value - previousTotalProducts.value);
});

const guestDifference = computed(() => {
  return Math.abs(totalGuests.value - previousGuests.value);
});

const salesChange = computed(() =>
  getPercentageChangeWithFlag(totalSalesAmount.value, previousTotalSales.value, p_salesAmount)
);

const profitChange = computed(() =>
  getPercentageChangeWithFlag(netProfits.value, previousNetProfits.value, p_netProfits)
);

const productChange = computed(() =>
  getPercentageChangeWithFlag(totalProductSales.value, previousTotalProducts.value, p_productSales)
);

const guestChange = computed(() =>
  getPercentageChangeWithFlag(totalGuests.value, previousGuests.value, p_totalGuests)
);
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

const groupedOrderProduct = computed(() => {
  const map = {}
  props.dataOrderProduct.forEach(item => {
    const kode = item.kode_product
    if (!map[kode]) {
      map[kode] = 0
    }
    map[kode] += item.quantity
  })

  return Object.entries(map).map(([kode_product, quantity]) => ({
    kode_product,
    quantity
  }))
})

const sortedOrderProduct = computed(() => {
  return groupedOrderProduct.value.sort((a, b) => b.quantity - a.quantity)
})
const sortedProductsByQuantity = computed(() => {
  return sortedOrderProduct.value.map(orderItem => {
    const matchedProduct = props.product.find(
      p => p.kode_product === orderItem.kode_product
    )
    return {
      ...matchedProduct,
      quantity: orderItem.quantity
    }
  }).filter(item => item.kode_product)
})

const filteredOrderData = computed(() => {
    if (!Array.isArray(props.dataOrder)) {
        console.error('dataOrder is not an array', props.dataOrder);
        return [];
    }
    const query = searchQuery.value.toLowerCase().trim();
    if (!query) {
        return [...props.dataOrder];
    }
    return props.dataOrder.filter(order => {
        return (
            (order.order_id && order.order_id.toString().toLowerCase().includes(query)) ||
            (order.nama_kasir && order.nama_kasir.toLowerCase().includes(query)) ||
            (order.order_type && order.order_type.toLowerCase().includes(query))
        );
    });
});

const formatDate = (isoString) => {
  return new Date(isoString).toLocaleString('id-ID', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit'
  });
};

const formatCurrency = (value) => {
        if (!value) return "0";
        return Number(value).toLocaleString('id-ID');
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
                    <a :href="route('pos')" class="back-to-pos w-[3.2rem] h-[3.2rem] flex items-center justify-center rounded-full bg-white text-[#2D71F8] cursor-pointer">
                        <i class="ri-arrow-go-back-fill text-current text-xl"></i>
                    </a>
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
                    <div @click="enableThisMonthSorting()" class="flex items-center justify-between h-[70%] w-32 rounded-full border border-slate-300 bg-transparent cursor-pointer">
                        <div class="text-sm font-medium text-slate-700 pl-3">This Month</div>
                        <div class="icon w-7 h-7 bg-[#f0f7ff] rounded-full flex items-center justify-center text-[#2D71F8] mr-1">
                            <i class="ri-calendar-line text-current text-md"></i>
                        </div>
                    </div>
                    <div @click="enableThreeMonthSorting()" class="flex items-center justify-between h-[70%] w-32 rounded-full border border-slate-300 bg-transparent cursor-pointer">
                        <div class="text-sm font-medium text-slate-700 pl-3">3 Month</div>
                        <div class="icon w-7 h-7 bg-[#f0f7ff] rounded-full flex items-center justify-center text-[#2D71F8] mr-1">
                            <i class="ri-calendar-line text-current text-md"></i>
                        </div>
                    </div>
                    <div @click="enableSixMonthSorting()"class="flex items-center justify-between h-[70%] w-32 rounded-full border border-slate-300 bg-transparent cursor-pointer">
                        <div class="text-sm font-medium text-slate-700 pl-3">6 Month</div>
                        <div class="icon w-7 h-7 bg-[#f0f7ff] rounded-full flex items-center justify-center text-[#2D71F8] mr-1">
                            <i class="ri-calendar-line text-current text-md"></i>
                        </div>
                    </div>
                    <div @click="enableOneYearSorting()"class="flex items-center justify-between h-[70%] w-24 rounded-full border border-slate-300 bg-transparent cursor-pointer">
                        <div class="text-sm font-medium text-slate-700 pl-3">1 Year</div>
                        <div class="icon w-7 h-7 bg-[#f0f7ff] rounded-full flex items-center justify-center text-[#2D71F8] mr-1">
                            <i class="ri-calendar-line text-current text-md"></i>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-center gap-3 pr-2">
                    <div class="text-sm font-medium text-slate-700 pl-3">{{ namaResto }} | <span class="text-gray-500">{{ namaOutlet }}</span></div>
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
                                    <div class="text-2xl md:text-3xl font-medium text-slate-700 shimmer">{{totalSalesAmount.toLocaleString('id-ID') }}</div>
                                    <div class="text-md text-gray-400 font-medium">IDR</div>
                                </div>
                            </div>
                            <div class="flex flex-row w-full h-[27%] justify-between items-center px-3">
                                <div v-if="p_salesAmount" class="flex items-center justify-center h-[68%] w-auto px-3 bg-[#fff5f5] rounded-full">
                                    <div class="text-xs font-medium text-[#FC4A4A]">-IDR {{salesDifference.toLocaleString('id-ID') }}</div>
                                </div>
                                <div v-else class="flex items-center justify-center h-[68%] w-auto px-3 bg-[#f2fff9] rounded-full">
                                    <div class="text-xs font-medium text-[#1C8370]">+IDR {{salesDifference.toLocaleString('id-ID') }}</div>
                                </div>
                                
                                <div v-if="p_salesAmount" class="flex items-center justify-center h-[68%] w-auto pl-3 pr-1 bg-[#fff5f5] rounded-full">
                                    <div class="text-xs font-medium text-[#FC4A4A]">-{{ salesChange }} %</div>
                                    <i class="ri-arrow-drop-down-fill text-[#FC4A4A] text-xl"></i>
                                </div>
                                <div v-else class="flex items-center justify-center h-[68%] w-auto pl-3 pr-1 bg-[#f2fff9] rounded-full">
                                    <div class="text-xs font-medium text-[#1C8370]">+{{ salesChange }} %</div>
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
                                    <div class="text-2xl md:text-3xl font-medium text-slate-700 shimmer">{{ totalProductSales.toLocaleString('id-ID') }}</div>
                                    <div class="text-md text-gray-400 font-medium">Items</div>
                                </div>
                            </div>
                            <div class="flex flex-row w-full h-[27%] justify-between items-center px-3">
                                <div v-if="p_productSales" class="flex items-center justify-center h-[68%] w-auto px-3 bg-[#fff5f5] rounded-full">
                                    <div class="text-xs font-medium text-[#FC4A4A]">-{{ productDifference.toLocaleString('id-ID') }} Items</div>
                                </div>
                                <div v-else class="flex items-center justify-center h-[68%] w-auto px-3 bg-[#f2fff9] rounded-full">
                                    <div class="text-xs font-medium text-[#1C8370]">+{{ productDifference.toLocaleString('id-ID') }} Items</div>
                                </div>
                                <div v-if="p_productSales" class="flex items-center justify-center h-[68%] w-auto pl-3 pr-1 bg-[#fff5f5] rounded-full">
                                    <div class="text-xs font-medium text-[#FC4A4A]">-{{ productChange }} %</div>
                                    <i class="ri-arrow-drop-down-fill text-[#FC4A4A] text-xl"></i>
                                </div>
                                <div v-else class="flex items-center justify-center h-[68%] w-auto pl-3 pr-1 bg-[#f2fff9] rounded-full">
                                    <div class="text-xs font-medium text-[#1C8370]">+{{ productChange }} %</div>
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
                                    <div class="text-2xl md:text-3xl font-medium text-slate-700 shimmer">{{ totalGuests.toLocaleString('id-ID') }}</div>
                                    <div class="text-md text-gray-400 font-medium">Persons</div>
                                </div>
                            </div>
                            <div class="flex flex-row w-full h-[27%] justify-between items-center px-3">
                                <!-- Jika p_totalGuests = true, artinya jumlah tamu menurun (merah) -->
                                <div v-if="p_totalGuests" class="flex items-center justify-center h-[68%] w-auto px-3 bg-[#fff5f5] rounded-full">
                                    <div class="text-xs font-medium text-[#FC4A4A]">-{{ guestDifference.toLocaleString('id-ID') }} Persons</div>
                                </div>
                                <!-- Jika p_totalGuests = false, artinya jumlah tamu meningkat (hijau) -->
                                <div v-else class="flex items-center justify-center h-[68%] w-auto px-3 bg-[#f2fff9] rounded-full">
                                    <div class="text-xs font-medium text-[#1C8370]">+{{ guestDifference.toLocaleString('id-ID') }} Persons</div>
                                </div>
                                
                                <div v-if="p_totalGuests" class="flex items-center justify-center h-[68%] w-auto pl-3 pr-1 bg-[#fff5f5] rounded-full">
                                    <div class="text-xs font-medium text-[#FC4A4A]">-{{ guestChange }} %</div>
                                    <i class="ri-arrow-drop-down-fill text-[#FC4A4A] text-xl"></i>
                                </div>
                                <div v-else class="flex items-center justify-center h-[68%] w-auto pl-3 pr-1 bg-[#f2fff9] rounded-full">
                                    <div class="text-xs font-medium text-[#1C8370]">+{{ guestChange }} %</div>
                                    <i class="ri-arrow-drop-up-fill text-[#1C8370] text-xl"></i>
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
                                    <div class="text-2xl md:text-3xl font-medium text-slate-700 shimmer">{{ netProfits.toLocaleString('id-ID') }}</div>
                                    <div class="text-md text-gray-400 font-medium">IDR</div>
                                </div>
                            </div>
                            <div class="flex flex-row w-full h-[27%] justify-between items-center px-3">
                                <div v-if="p_netProfits" class="flex items-center justify-center h-[68%] w-auto px-3 bg-[#fff5f5] rounded-full">
                                    <div class="text-xs font-medium text-[#FC4A4A]">-IDR {{ profitDifference.toLocaleString('id-ID') }}</div>
                                </div>
                                <div v-else class="flex items-center justify-center h-[68%] w-auto px-3 bg-[#f2fff9] rounded-full">
                                    <div class="text-xs font-medium text-[#1C8370]">+IDR {{ profitDifference.toLocaleString('id-ID') }}</div>
                                </div>
                                <div v-if="p_netProfits" class="flex items-center justify-center h-[68%] w-auto pl-3 pr-1 bg-[#fff5f5] rounded-full">
                                    <div class="text-xs font-medium text-[#FC4A4A]">-{{ profitChange }} %</div>
                                    <i class="ri-arrow-drop-down-fill text-[#FC4A4A] text-xl"></i>
                                </div>
                                <div v-else class="flex items-center justify-center h-[68%] w-auto pl-3 pr-1 bg-[#f2fff9] rounded-full">
                                    <div class="text-xs font-medium text-[#1C8370]">+{{ profitChange }} %</div>
                                    <i class="ri-arrow-drop-up-fill text-[#1C8370] text-xl"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <div class="more-reports">
                <div class="flex w-full flex-col lg:flex-row gap-4 h-auto">
                    <div class="fav-product flex flex-col w-full lg:w-[30%] bg-white rounded-3xl px-4 h-auto">
                        <div class="flex w-full h-16 items-center gap-3 flex-row pl-3">
                            <div class="w-2 h-2 rounded-full bg-[#2D71F8] mt-1"></div>
                            <div class="text-lg md:text-xl text-slate-700 font-normal">Favorite Product</div>
                        </div>
                        
                        <div class="flex flex-row w-full h-11 gap-2 mb-3">
                            <div class="image-label text-xs md:text-sm flex items-center justify-center px-3 md:px-5 rounded-2xl text-slate-500 border border-grey-400">Img</div>
                            <div class="image-label text-xs md:text-sm flex-1 flex items-center justify-center rounded-2xl text-slate-500 border border-grey-400">Product Name</div>
                            <div class="image-label text-xs md:text-sm flex items-center justify-center px-2 md:px-4 rounded-2xl text-slate-500 border border-grey-400">Orders</div>
                        </div>
                        
                        <div class="flex flex-col w-full overflow-y-auto h-auto xl:h-[24rem] lg:h-[28rem] space-y-3 py-2">
                            <div v-for="product in sortedProductsByQuantity" :key="product.kode_product" 
                                class="flex flex-row w-full h-16 md:h-20 items-center border-b border-zinc-200 justify-between pb-3">
                                <div class="p_image flex items-center justify-center bg-zinc-100 w-10 h-10 md:w-14 md:h-14 rounded-xl">
                                    <img class="max-h-10 w-auto object-contain":src="'http://127.0.0.1:8000/storage/' + product.foto_product"alt=""/>
                                </div>
                                <div class="flex flex-col items-start justify-center flex-1 px-3">
                                    <div class="text-xs md:text-sm font-medium text-slate-700 pb-1 truncate w-full">{{ product.nama_product }}</div>
                                    <div class="flex items-center justify-center px-3 py-1 rounded-xl text-xs font-medium" 
                                        :style="{ backgroundColor: product.kategori?.warna_background_kategori, color: product.kategori?.warna_teks_kategori }">
                                        {{ product.kategori?.nama_kategori }}
                                    </div>
                                </div>
                                <div class="text-sm md:text-base text-slate-700 font-medium whitespace-nowrap pr-4">
                                    {{product.quantity}} <span class="font-normal text-zinc-400">x</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="all-order flex flex-col h-auto lg:h-[28rem] xl:h-[24rem] w-full lg:w-[70%] bg-white rounded-3xl px-4">
                        <div class="flex w-full justify-between h-56 items-center flex-row pl-3">
                            <div class="flex flex-row justify-center items-center gap-3">
                                <div class="w-2 h-2 rounded-full bg-[#2D71F8] mt-1"></div>
                                <div class="text-lg md:text-xl text-slate-700 font-normal">All Orders</div>
                            </div>
                            <div class="flex flex-row justify-center items-center mr-5">
                                <div class="flex flex-row h-[70%] w-full">
                                    <div class="flex items-center w-full pl-1 h-[60%] bg-white rounded-full mx-4 border">
                                        <div class="icon flex items-center justify-center w-8 h-8 bg-gray-100 rounded-full shrink-0 text-gray-700">
                                            <i class="bi bi-search text-sm text-current"></i>
                                        </div>
                                        <input v-model="searchQuery" type="text" class="flex-1 bg-transparent px-4 placeholder:text-gray-400 text-gray-700 font-semibold border-none focus:outline-none focus:ring-0" placeholder="Search Order"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-row w-full h-36 gap-2 mb-3 overflow-x-auto pb-2">
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
                                <div v-if="filteredOrderData.length === 0" class="flex justify-center items-center h-20 text-gray-500">
                                    No orders found
                                </div>
                                <div v-for="(order, index) in filteredOrderData" :key="order.id" class="flex flex-row w-full h-10 md:h-[2.5rem] gap-2 mb-3">
                                    <div class="label text-xs md:text-sm flex-shrink-0 flex items-center justify-center min-w-16 md:min-w-24 px-2 md:px-4 rounded-2xl text-slate-700 font-medium">
                                        #{{ order.order_id }}
                                    </div>
                                    <div class="label text-[0.6rem] md:text-[0.73rem] flex-shrink-0 flex items-center justify-center min-w-14 md:w-18 xl:w-20 text-center px-2 md:px-4 rounded-2xl bg-[#f0f7ff] text-[#2D71F8] font-medium">
                                        {{ order.order_type || 'N/A' }}
                                    </div>
                                    <div class="label text-xs md:text-sm flex-shrink-0 flex items-center justify-center min-w-24 md:min-w-36 px-2 md:px-4 rounded-2xl text-slate-700 font-medium truncate">
                                        {{ order.nama_kasir || 'N/A' }}
                                    </div>
                                    <div class="label text-xs md:text-sm flex-shrink-0 flex items-center justify-center min-w-28 md:min-w-40 px-2 md:px-4 rounded-2xl text-slate-700 font-medium">
                                        {{ formatDate(order.created_at) || 'N/A' }}
                                    </div>
                                    <div class="label text-xs md:text-sm flex-shrink-0 flex items-center justify-center min-w-16 md:min-w-24 px-2 md:px-4 rounded-2xl text-slate-700 font-medium">
                                        {{ order.guest || '0' }}
                                    </div>
                                    <div class="label text-xs md:text-sm flex-shrink-0 flex items-center justify-center min-w-32 md:min-w-44 px-2 md:px-4 rounded-2xl text-slate-700 font-medium">
                                        Rp{{ formatCurrency(order.total_harga_after_all) }},00
                                    </div>
                                    <div class="label text-xs md:text-sm flex-shrink-0 flex items-center justify-center min-w-16 md:min-w-24 px-2 md:px-4 rounded-2xl text-slate-700 font-medium">
                                        {{ order.status || 'DONE' }}
                                    </div>
                                    <div @click="openOrderDetailModal(order)" class="label text-xs md:text-sm flex-shrink-0 flex items-center justify-center min-w-20 md:min-w-28 px-2 md:px-4 rounded-2xl text-[#2D71F8] underline font-medium cursor-pointer">
                                        Detail
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="isOrderDetailModalOpen" class="fixed inset-0 flex items-center justify-center bg-slate-400 bg-opacity-50"@click.self="closeOrderDetailModal">
                    <div class="absolute top-1/2 left-1/3 transform -translate-x-[2rem] -translate-y-1/2 bg-white rounded-2xl w-[35rem] shadow-2xl">
                        <div class="flex flex-row justify-between items-center justify-center shadow-lg shadow-gray-100 rounded-md p-3 pb-3">
                            <div class="flex w-9 h-9 bg-white"></div>
                                <div class="flex flex-col justify-center items-center">
                                    <h2 class="text-normal font-normal">Order Detail</h2>
                                </div>
                                <div class="flex items-center justify-center rounded-full bg-[#fff2f3] w-9 h-9 text-[#FC4A4A] cursor-pointer" @click="closeOrderDetailModal">
                                    <i class="bi bi-x-lg text-current"></i>
                                </div>
                            </div>
                            <div class="flex w-full flex-col max-h-[34rem] overflow-auto mb-5">
                                <div class="flex flex-col w-full h-50 py-4 mb-1 px-5 justify-between">
                                    <div class="flex flex-row justify-left items-center w-full h-auto mb-2">
                                    <div class="text-xl italic font-medium mr-11 text-gray-800">#{{ selectedOrder?.order_id }}</div>
                                    <div class="flex justify-center items-center bg-[#f2fff9] text-sm text-[#1C8370] rounded-full py-[0.100rem] px-4">All Done</div>
                                </div>
                                <div class="flex flex-row w-auto pb-4 border-b-2 border-dashed text-gray-500">
                                    <div class="flex flex-col mr-8 w-auto">
                                        <div class="text-[0.920rem]">Table: {{ selectedOrder?.tables }}</div>
                                        <div class="text-[0.920rem]">Guest: {{ selectedOrder?.guest }} Guests</div>
                                    </div>
                                    <div class="flex flex-col mr-8 w-auto">
                                        <div class="text-[0.920rem]">Order type: {{ selectedOrder?.order_type }}</div>
                                        <div class="text-[0.920rem]">Cashier: {{ selectedOrder?.nama_kasir }}</div>
                                    </div>
                                    <div class="flex flex-col">
                                        <div class="text-[0.920rem]">Date Order:</div>
                                        <div class="flex justify-center items-start text-[0.920rem]">{{ formattedDataOrderUpdatedAt }}</div>
                                    </div>
                                </div>
                                <div class="flex flex-col justify-start items-start text-gray-500 w-full h-auto pt-3 pb-4 border-dashed border-b-2">
                                    <div class="text-[0.920rem]">Total Price: Rp{{ formatCurrency(selectedOrder?.total_harga_after_all) }}</div>
                                    <div class="text-[0.920rem]">Rounding: {{ selectedOrder?.rounding }}</div>
                                    <div class="text-[0.920rem]">Amount Paid: Rp{{ formatCurrency(selectedOrder?.amount_paid) }}</div>
                                    <div class="text-[0.920rem]">Change: Rp{{ formatCurrency(selectedOrder?.change) }}</div>
                                </div>
                            </div>
                            <div class="flex w-full h-auto flex-col">
                                <div class="flex flex-col max-h-40 w-full mb-4 pb-4 overflow-y-auto shadow-lg shadow-gray-100 rounded-md">
                                    <div v-for="product in selectedProducts" :key="product.id" class="text-[0.940rem] text-gray-500 px-5 mb-2">
                                        <span class="mr-3">{{ product.quantity }}x</span>{{ product.nama_product }}
                                    </div>
                                </div>
                                <div class="px-5 text-[0.940rem] text-gray-500 flex items-center">
                                    <span class="mr-3">Total Order:</span>{{ selectedProducts.length }} Items
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
</template>