<template>
    <div>
        <inertia-link href="/tournaments/create" class="btn"
            >Создать турнир</inertia-link
        >
        <div v-for="tournament in tournaments" :key="tournament.id">
            <p>
                {{ tournament.name }} ({{
                    tournament.mode === "1v1" ? "1 на 1" : "Команда"
                }})
            </p>
            <button @click="register(tournament.id)">Зарегистрироваться</button>
        </div>
    </div>
</template>

<script>
export default {
    props: ["tournaments"],
    methods: {
        register(tournamentId) {
            this.$inertia.post(`/tournaments/${tournamentId}/register`);
        },
    },
};
</script>
