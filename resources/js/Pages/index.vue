<script>
import MainLayout from "@/Layouts/MainLayout.vue";
import { computed, onBeforeMount, onMounted, reactive, ref, watch } from "vue";
export default {
    layout: MainLayout
};
</script>

<script setup>
import getPreviousTime from "@/Utils/getPreviousTime";
import Table from "@/Components/Table.vue";
import dateFormatter from "@/Utils/dateFormatter";
import axios from "axios";
import { Head } from "@inertiajs/vue3";

const weeklyData = ref([]);
const categories = ref([
    getPreviousTime(undefined, 6),
    getPreviousTime(undefined, 5),
    getPreviousTime(undefined, 4),
    getPreviousTime(undefined, 3),
    getPreviousTime(undefined, 2),
    getPreviousTime(undefined, 1),
    Date.now()
]);
const chartOptions = reactive({
    chart: {
        id: "vuechart-example"
    },
    xaxis: {
        categories: [
            dateFormatter(categories.value[0], false),
            dateFormatter(categories.value[1], false),
            dateFormatter(categories.value[2], false),
            dateFormatter(categories.value[3], false),
            dateFormatter(categories.value[4], false),
            dateFormatter(categories.value[5], false),
            dateFormatter(categories.value[6], false)
        ]
    }
});
const series = ref([]);

const datesAreOnSameDay = (one, two) => {
    let result = false;
    const dateOne = new Date(one);
    const dateTwo = new Date(two);

    if (
        dateOne.getFullYear() === dateTwo.getFullYear() &&
        dateOne.getMonth() === dateTwo.getMonth() &&
        dateOne.getDay() === dateTwo.getDay()
    ) {
        result = true;
    }
    return result;
};

const getWeeklyData = async () => {
    const { data } = await axios.get(
        `/history/sales?from=${Math.floor(getPreviousTime(undefined, 6) / 1000)}&to=${Math.floor(Date.now() / 1000)}&mode=print`
    );
    return data;
};

onBeforeMount(async () => {
    weeklyData.value = await getWeeklyData();
});

watch(
    () => weeklyData.value,
    () => {
        let data = [0, 0, 0, 0, 0, 0, 0];

        categories.value.forEach((categoryItem, categoryIndex) => {
            weeklyData.value.forEach((weeklyItem, weeklyIndex) => {
                if (datesAreOnSameDay(new Date(categoryItem), new Date(weeklyItem.created_at).getTime())) {
                    data[categoryIndex] += 1;
                }
            });
        });

        series.value = [
            {
                name: "Total Sales",
                data
            }
        ];
    },
    { deep: true }
);

const getTodaySalesTotal = computed(() => {
    let count = 0;

    weeklyData.value.forEach(item => {
        if (datesAreOnSameDay(new Date(item.created_at).getTime(), new Date().getTime()) === true) {
            count += 1;
        }
    });

    return count;
});

const getTodayRevenueTotal = computed(() => {
    let revenue = 0;

    weeklyData.value.forEach(item => {
        if (datesAreOnSameDay(new Date(item.created_at).getTime(), new Date().getTime()) === true) {
            revenue += item.amount;
        }
    });

    return revenue;
});
</script>

<template>
    <Head>
        <title>Dashboard</title>
    </Head>
    <h1 class="main__title">Dashboard</h1>

    <div class="container">
        <div class="container__row">
            <div class="chart">
                <h1 class="chart__title" v-text="'Weekly Sales'" />
                <apexchart width="500" type="bar" :options="chartOptions" :series="series" />
            </div>

            <div class="container__widget">
                <div class="container-small">
                    <i class="iconoir-data-transfer-both container-small__icon" />
                    <h2 class="container-small__title">Today sales</h2>
                    <div class="container-small__count" v-text="getTodaySalesTotal" />
                </div>
                <div class="container-small">
                    <i class="iconoir-dollar container-small__icon" />
                    <h2 class="container-small__title">Today Revenue</h2>
                    <div class="container-small__count" v-text="getTodayRevenueTotal" />
                </div>
            </div>
        </div>

        <div class="container__row">
            <div class="container__right">
                <h1 class="container__title" v-text="'Recent Sales'" />
                <Table>
                    <template #head>
                        <tr>
                            <td>INVOICE ID</td>
                            <td>ITEMS</td>
                            <td>AMOUNT</td>
                            <td>DATE & TIME</td>
                            <td>CASHIER</td>
                        </tr>
                    </template>
                    <template #body>
                        <tr data-empty="true" v-if="item in weeklyData == 0">
                            <td colspan="100">No items available</td>
                        </tr>
                        <tr v-for="item in weeklyData">
                            <td v-text="item.id" />
                            <td v-text="item.items.length" />
                            <td v-text="item.amount" />
                            <td v-text="dateFormatter(item.created_at)" />
                            <td v-text="item.user.name" />
                        </tr>
                    </template>
                </Table>
            </div>
        </div>
    </div>
</template>

<style lang="scss" scoped>
@use "./../../css/app.scss";
.chart {
    background-color: var(--color-5);
    border-radius: 4px;
    padding: 1.5rem 2rem;
    display: flex;
    flex-direction: column;
    row-gap: 2rem;
    border: 1px solid var(--color-4);
    max-width: fit-content;

    &__title {
        font-size: 1.5rem;
        font-weight: 500;
        color: var(--color-1);
    }
}

.container {
    display: flex;
    flex-direction: column;
    gap: 2rem;

    &__title {
        font-size: 1.5rem;
        font-weight: 500;
        color: var(--color-1);
    }

    &__row {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    &__widget {
        display: flex;
        gap: 1rem;

        @include app.screen(lg) {
            flex-direction: column;
        }
    }

    &__right {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
}

.container-small {
    padding: 1rem;
    border-radius: 4px;
    border: 1px solid var(--color-4);
    background-color: var(--color-5);
    display: flex;
    flex-direction: column;
    row-gap: 1rem;
    width: 18rem;

    &__icon {
        font-size: 1.8rem;
    }

    &__title {
        font-size: 1.2rem;
    }

    &__count {
        color: var(--color-2);
        font-size: 3.5rem;
    }
}
</style>
