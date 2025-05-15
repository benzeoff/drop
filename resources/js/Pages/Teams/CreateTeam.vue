<!-- resources/js/Pages/Teams/CreateTeam.vue -->
<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";

// Define props
const props = defineProps({
    users: {
        type: Array,
        default: () => [],
    },
});

// Initialize the form
const teamForm = useForm({
    name: "",
    logo: "",
    description: "",
    max_players: 2,
    user_ids: [],
});

// Define the submit function
const submit = () => {
    teamForm.post(route("dashboard.teams.store"), {
        onSuccess: () => teamForm.reset(),
        onError: (errors) => {
            console.log(errors); // Log errors for debugging
        },
    });
};
</script>

<template>
    <Head title="Создать команду - Drop Cyber Lounge" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Создать команду
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit" class="space-y-4">
                            <div>
                                <label class="text-sm text-gray-600"
                                    >Название команды</label
                                >
                                <input
                                    v-model="teamForm.name"
                                    placeholder="Название команды"
                                    required
                                    class="w-full p-2 border rounded"
                                />
                                <p
                                    v-if="teamForm.errors.name"
                                    class="text-red-500 text-sm"
                                >
                                    {{ teamForm.errors.name }}
                                </p>
                            </div>
                            <div>
                                <label class="text-sm text-gray-600"
                                    >URL логотипа</label
                                >
                                <input
                                    v-model="teamForm.logo"
                                    placeholder="URL логотипа"
                                    class="w-full p-2 border rounded"
                                />
                                <p
                                    v-if="teamForm.errors.logo"
                                    class="text-red-500 text-sm"
                                >
                                    {{ teamForm.errors.logo }}
                                </p>
                            </div>
                            <div>
                                <label class="text-sm text-gray-600"
                                    >Описание команды</label
                                >
                                <textarea
                                    v-model="teamForm.description"
                                    placeholder="Описание команды"
                                    class="w-full p-2 border rounded"
                                ></textarea>
                                <p
                                    v-if="teamForm.errors.description"
                                    class="text-red-500 text-sm"
                                >
                                    {{ teamForm.errors.description }}
                                </p>
                            </div>
                            <div>
                                <label class="text-sm text-gray-600"
                                    >Максимум игроков</label
                                >
                                <select
                                    v-model="teamForm.max_players"
                                    required
                                    class="w-full p-2 border rounded"
                                >
                                    <option
                                        v-for="n in [2, 3, 4, 5]"
                                        :key="n"
                                        :value="n"
                                    >
                                        {{ n }}
                                    </option>
                                </select>
                                <p
                                    v-if="teamForm.errors.max_players"
                                    class="text-red-500 text-sm"
                                >
                                    {{ teamForm.errors.max_players }}
                                </p>
                            </div>
                            <div>
                                <label class="text-sm text-gray-600"
                                    >Участники</label
                                >
                                <select
                                    v-model="teamForm.user_ids"
                                    multiple
                                    required
                                    class="w-full p-2 border rounded"
                                >
                                    <option
                                        v-for="user in users"
                                        :key="user.id"
                                        :value="user.id"
                                    >
                                        {{ user.name }}
                                    </option>
                                </select>
                                <p
                                    v-if="teamForm.errors.user_ids"
                                    class="text-red-500 text-sm"
                                >
                                    {{ teamForm.errors.user_ids }}
                                </p>
                            </div>
                            <button
                                type="submit"
                                class="rounded-md bg-green-500 px-3 py-1 text-white hover:bg-green-600"
                            >
                                Создать команду
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
