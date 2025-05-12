<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import { ref, computed, watch } from "vue";

// Текущее время для ограничения выбора
const now = new Date();
const minDateTime = computed(() => {
    return now.toISOString().slice(0, 16); // Формат для datetime-local
});

const props = defineProps({
    resources: Array, // Объединили компьютеры и зоны
    pricings: Array,
});

const form = useForm({
    resource_id: null,
    start_time: "",
    duration: 30,
    package_type: "standard",
});

const packageTypes = ["standard", "day", "night"];

// Длительности, зависящие от типа пакета
const durations = computed(() => {
    switch (form.package_type) {
        case "standard":
            return [30, 60]; // 30 минут, 1 час
        case "day":
        case "night":
            return [180, 300, 720]; // 3 часа, 5 часов, 12 часов
        default:
            return [30, 60];
    }
});

// Вычисляемая стоимость
const totalPrice = computed(() => {
    if (!form.resource_id || !form.duration || !form.package_type) return 0;

    const resource = props.resources.find(r => r.id === form.resource_id);
    if (!resource) return 0;

    const category = resource.category || resource.name;
    const type = resource.type;

    const pricing = props.pricings.find(p =>
        p.type === type &&
        p.category === category &&
        p.package_type === form.package_type &&
        p.duration === form.duration
    );

    return pricing ? pricing.price : 0;
});

function submit() {
    form.post(route("booking.store"), {
        onSuccess: () => form.reset(),
    });
}
</script>

<template>
    <Head title="Бронирование - Drop Cyber Lounge" />

    <div class="min-h-screen bg-black text-white p-6">
        <h2 class="text-3xl font-bold text-red-500 mb-8 text-center">
            Забронируй своё место в Drop Cyber Lounge!
        </h2>

        <form @submit.prevent="submit" class="space-y-6 max-w-md mx-auto">
            <!-- Тип пакета -->
            <div>
                <label class="block text-sm font-medium text-gray-300"
                    >Тип пакета</label
                >
                <select
                    v-model="form.package_type"
                    class="mt-2 block w-full rounded-md border-gray-600 bg-gray-800 text-gray-300 focus:border-red-500 focus:ring-red-500 sm:text-sm py-2 px-3"
                >
                    <option
                        v-for="type in packageTypes"
                        :key="type"
                        :value="type"
                    >
                        {{
                            type === "day"
                                ? "День (9:00–22:00)"
                                : type === "night"
                                ? "Ночь (22:00–8:00)"
                                : "Стандарт"
                        }}
                    </option>
                </select>
                <p
                    v-if="form.errors.package_type"
                    class="mt-2 text-sm text-red-500"
                >
                    {{ form.errors.package_type }}
                </p>
            </div>

            <!-- Ресурс -->
            <div>
                <label class="block text-sm font-medium text-gray-300"
                    >Выберите ресурс</label
                >
                <select
                    v-model="form.resource_id"
                    class="mt-2 block w-full rounded-md border-gray-600 bg-gray-800 text-gray-300 focus:border-red-500 focus:ring-red-500 sm:text-sm py-2 px-3"
                >
                    <option value="" disabled>Выберите ресурс</option>
                    <option
                        v-for="resource in resources"
                        :key="resource.id"
                        :value="resource.id"
                    >
                        {{ resource.name }} ({{
                            resource.category || resource.name
                        }})
                    </option>
                </select>
                <p
                    v-if="form.errors.resource_id"
                    class="mt-2 text-sm text-red-500"
                >
                    {{ form.errors.resource_id }}
                </p>
            </div>

            <!-- Дата и время -->
            <div>
                <label class="block text-sm font-medium text-gray-300"
                    >Дата и время начала</label
                >
                <input
                    type="datetime-local"
                    v-model="form.start_time"
                    :min="minDateTime"
                    class="mt-2 block w-full rounded-md border-gray-600 bg-gray-800 text-gray-300 focus:border-red-500 focus:ring-red-500 sm:text-sm py-2 px-3"
                />
                <p
                    v-if="form.errors.start_time"
                    class="mt-2 text-sm text-red-500"
                >
                    {{ form.errors.start_time }}
                </p>
            </div>

            <!-- Длительность -->
            <div>
                <label class="block text-sm font-medium text-gray-300"
                    >Длительность</label
                >
                <select
                    v-model="form.duration"
                    class="mt-2 block w-full rounded-md border-gray-600 bg-gray-800 text-gray-300 focus:border-red-500 focus:ring-red-500 sm:text-sm py-2 px-3"
                >
                    <option
                        v-for="duration in durations"
                        :key="duration"
                        :value="duration"
                    >
                        {{ duration }} минут
                    </option>
                </select>
                <p
                    v-if="form.errors.duration"
                    class="mt-2 text-sm text-red-500"
                >
                    {{ form.errors.duration }}
                </p>
            </div>

            <!-- Стоимость -->
            <div>
                <label class="block text-sm font-medium text-gray-300"
                    >Стоимость</label
                >
                <p class="mt-2 text-lg font-semibold text-green-400">
                    {{ totalPrice }} руб.
                </p>
            </div>

            <!-- Кнопка -->
            <div class="text-center">
                <button
                    type="submit"
                    class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                >
                    Забронировать
                </button>
            </div>
        </form>
    </div>
</template>

<style>
.border-gray-600 {
    border-color: #444;
}

.focus\:ring-red-500:focus {
    box-shadow: 0 0 0 3px rgba(255, 0, 0, 0.5);
}

.bg-gray-800 {
    background: #222;
}

button.bg-red-600 {
    background: #ff0000;
    box-shadow: 0 0 15px rgba(255, 0, 0, 0.5);
    transition: all 0.3s ease;
}

button.bg-red-600:hover {
    background: #e60000;
    box-shadow: 0 0 20px rgba(255, 0, 0, 0.8);
}
</style>
