<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, usePage } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import axios from "axios";
import dayjs from "dayjs";

const props = defineProps({
    notifications: Array,
});

const notifications = ref(props.notifications || []);
const filter = ref("all"); // Реактивная переменная для фильтра

// Вычисляемое свойство для фильтрации уведомлений
const filteredNotifications = computed(() =>
    filter.value === "all"
        ? notifications.value
        : notifications.value.filter((n) => !n.read_at)
);

// Форматирование даты
const formatDate = (date) => dayjs(date).format("DD.MM.YYYY HH:mm");

const markAsRead = async (notificationId) => {
    try {
        await axios.put(route("notifications.read", notificationId));
        notifications.value = notifications.value.map((n) =>
            n.id === notificationId
                ? { ...n, read_at: new Date().toISOString() }
                : n
        );
    } catch (error) {
        console.error(
            "Ошибка при отметке уведомления как прочитанного:",
            error
        );
    }
};

const markAllAsRead = async () => {
    try {
        await axios.put(route("notifications.read-all"));
        notifications.value = notifications.value.map((n) => ({
            ...n,
            read_at: new Date().toISOString(),
        }));
    } catch (error) {
        console.error(
            "Ошибка при отметке всех уведомлений как прочитанных:",
            error
        );
    }
};

const deleteNotification = async (notificationId) => {
    if (!confirm("Вы уверены, что хотите удалить это уведомление?")) {
        return;
    }
    try {
        await axios.delete(route("notifications.destroy", notificationId));
        notifications.value = notifications.value.filter(
            (n) => n.id !== notificationId
        );
    } catch (error) {
        console.error("Ошибка при удалении уведомления:", error);
    }
};

console.log("Notifications:", props.notifications);
</script>

<template>
    <Head title="Уведомления - Drop Cyber Lounge" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Уведомления
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">
                                Ваши уведомления
                            </h3>
                            <button
                                v-if="notifications.length"
                                @click="markAllAsRead"
                                class="text-sm text-white bg-[#FF2D20] hover:bg-[#CC1A1A] px-3 py-1 rounded-md"
                            >
                                Отметить все как прочитанные
                            </button>
                        </div>

                        <!-- Кнопки фильтрации -->
                        <div class="flex gap-4 mb-4">
                            <button
                                @click="filter = 'all'"
                                :class="[
                                    'text-sm px-3 py-1 rounded-md',
                                    filter === 'all'
                                        ? 'bg-[#FF2D20] text-white'
                                        : 'bg-gray-200 text-gray-700',
                                ]"
                            >
                                Все
                            </button>
                            <button
                                @click="filter = 'unread'"
                                :class="[
                                    'text-sm px-3 py-1 rounded-md',
                                    filter === 'unread'
                                        ? 'bg-[#FF2D20] text-white'
                                        : 'bg-gray-200 text-gray-700',
                                ]"
                            >
                                Непрочитанные
                            </button>
                        </div>

                        <div
                            v-if="filteredNotifications.length"
                            class="space-y-4"
                        >
                            <div
                                v-for="notification in filteredNotifications"
                                :key="notification.id"
                                :class="[
                                    'p-4 rounded-md border',
                                    notification.read_at
                                        ? 'bg-gray-100 border-gray-200'
                                        : 'bg-white border-[#FF2D20]',
                                ]"
                            >
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4
                                            class="text-md font-medium text-gray-900"
                                        >
                                            {{ notification.title }}
                                        </h4>
                                        <p class="text-sm text-gray-600 mt-1">
                                            {{ notification.message }}
                                        </p>
                                        <p class="text-xs text-gray-500 mt-2">
                                            {{
                                                formatDate(
                                                    notification.created_at
                                                )
                                            }}
                                        </p>
                                    </div>
                                    <div class="flex gap-2">
                                        <button
                                            v-if="!notification.read_at"
                                            @click="markAsRead(notification.id)"
                                            class="text-sm text-[#FF2D20] hover:text-[#CC1A1A]"
                                        >
                                            Прочитано
                                        </button>
                                        <button
                                            @click="
                                                deleteNotification(
                                                    notification.id
                                                )
                                            "
                                            class="text-sm text-red-500 hover:text-red-700"
                                        >
                                            Удалить
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-sm text-gray-600">
                            Уведомлений пока нет.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
@font-face {
    font-family: "Drop";
    src: url("/fonts/drop.ttf") format("truetype");
    font-weight: normal;
    font-style: normal;
}

body {
    font-family: "Drop", sans-serif;
}
</style>
