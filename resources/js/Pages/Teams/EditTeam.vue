<!-- resources/js/Pages/EditTeam.vue -->
<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";

const props = defineProps({
    team: {
        type: Object,
        required: true,
    },
    users: {
        type: Array,
        default: () => [],
    },
});

// Safeguard against undefined team
if (!props.team || !props.team.id) {
    throw new Error("Team data is missing or invalid");
}

const form = useForm({
    name: props.team.name || "",
    invite_code: props.team.invite_code || "",
    description: props.team.description || "",
    max_players: props.team.max_players || 5,
    remove_user_ids: [],
    add_user_ids: [],
});

const submit = () => {
    form.put(route("dashboard.teams.update", props.team.id), {
        onSuccess: () => {
            window.history.back();
        },
        onError: (errors) => {
            console.log(errors);
        },
    });
};
</script>

<template>
    <Head title="Редактировать команду" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Редактировать команду: {{ team.name }}
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
                                    v-model="form.name"
                                    required
                                    class="w-full p-2 border rounded"
                                />
                                <p
                                    v-if="form.errors.name"
                                    class="text-red-500 text-sm"
                                >
                                    {{ form.errors.name }}
                                </p>
                            </div>
                            <div>
                                <label class="text-sm text-gray-600"
                                    >Код приглашения</label
                                >
                                <input
                                    v-model="form.invite_code"
                                    required
                                    class="w-full p-2 border rounded"
                                />
                                <p
                                    v-if="form.errors.invite_code"
                                    class="text-red-500 text-sm"
                                >
                                    {{ form.errors.invite_code }}
                                </p>
                            </div>
                            <div>
                                <label class="text-sm text-gray-600"
                                    >Описание</label
                                >
                                <textarea
                                    v-model="form.description"
                                    class="w-full p-2 border rounded"
                                ></textarea>
                                <p
                                    v-if="form.errors.description"
                                    class="text-red-500 text-sm"
                                >
                                    {{ form.errors.description }}
                                </p>
                            </div>
                            <div>
                                <label class="text-sm text-gray-600"
                                    >Максимальное количество игроков</label
                                >
                                <input
                                    v-model.number="form.max_players"
                                    type="number"
                                    min="2"
                                    max="10"
                                    required
                                    class="w-full p-2 border rounded"
                                />
                                <p
                                    v-if="form.errors.max_players"
                                    class="text-red-500 text-sm"
                                >
                                    {{ form.errors.max_players }}
                                </p>
                            </div>

                            <!-- Add/Remove Users (Optional) -->
                            <div v-if="users.length > 0">
                                <label class="text-sm text-gray-600"
                                    >Добавить участников</label
                                >
                                <select
                                    v-model="form.add_user_ids"
                                    multiple
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
                                    v-if="form.errors.add_user_ids"
                                    class="text-red-500 text-sm"
                                >
                                    {{ form.errors.add_user_ids }}
                                </p>
                            </div>

                            <div>
                                <label class="text-sm text-gray-600"
                                    >Удалить участников</label
                                >
                                <select
                                    v-model="form.remove_user_ids"
                                    multiple
                                    class="w-full p-2 border rounded"
                                >
                                    <option
                                        v-for="user in team.users"
                                        :key="user.id"
                                        :value="user.id"
                                        :disabled="user.id === team.captain_id"
                                    >
                                        {{ user.name }}
                                    </option>
                                </select>
                                <p
                                    v-if="form.errors.remove_user_ids"
                                    class="text-red-500 text-sm"
                                >
                                    {{ form.errors.remove_user_ids }}
                                </p>
                            </div>

                            <button
                                type="submit"
                                class="rounded-md bg-blue-500 px-3 py-1 text-white hover:bg-blue-600"
                            >
                                Сохранить
                            </button>
                            <button
                                @click="window.history.back()"
                                class="rounded-md bg-gray-500 px-3 py-1 text-white hover:bg-gray-600 ml-2"
                            >
                                Отмена
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
