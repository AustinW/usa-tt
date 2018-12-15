<template>
    <div>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th><input type="checkbox" @change="checkAll" checked="checked" /></th>
                    <th>Name</th>
                    <th><input type="checkbox" @change="checkTra" checked="checked" /> TRA</th>
                    <th><input type="checkbox" @change="checkDmt" checked="checked" /> DMT</th>
                    <th><input type="checkbox" @change="checkTum" checked="checked" /> TUM</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="athlete in athletes" :key="athlete.id">
                    <td><input type="checkbox" v-model="athlete.selected" /></td>
                    <td>{{ athlete.name }}</td>
                    <td><input type="checkbox" v-model="athlete.trampoline" :disabled="athlete.trampoline === null" /></td>
                    <td><input type="checkbox" v-model="athlete.double_mini" :disabled="athlete.double_mini === null" /></td>
                    <td><input type="checkbox" v-model="athlete.tumbling" :disabled="athlete.tumbling === null" /></td>
                </tr>
            </tbody>
        </table>

        <button @click="download">Download</button>
    </div>
</template>

<script>
    export default {
        name: 'Athletes',

        data: () => ({
            athletes: []
        }),

        created() {
            const roster = window.athletes;

            Object.keys(roster).forEach((name, id) => {
                this.athletes.push({
                    id,
                    name,
                    selected: true,
                    trampoline: roster[name].trampoline,
                    double_mini: roster[name].double_mini,
                    tumbling: roster[name].tumbling,
                });
            });
        },

        methods: {
            checkAll(event) {
                this.athletes.forEach((athlete) => {
                    athlete.selected = event.target.checked;
                });
            },

            checkTra(event) {
                return this.checkEvent('trampoline', event);
            },

            checkDmt(event) {
                return this.checkEvent('double_mini', event);
            },

            checkTum(event) {
                return this.checkEvent('tumbling', event);
            },

            checkEvent(apparatus, event) {
                this.athletes.forEach((athlete) => {
                    athlete[apparatus] = event.target.checked;
                });
            },

            download() {
                window.axios.post('/download/select', {
                    tra: this.athletes.filter(athlete => athlete.selected).filter(athlete => athlete.trampoline),
                    dmt: this.athletes.filter(athlete => athlete.selected).filter(athlete => athlete.double_mini),
                    tum: this.athletes.filter(athlete => athlete.selected).filter(athlete => athlete.tumbling),
                })
            }
        }
    }
</script>
