<script setup>
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    fecha: '',
    titulo: '',
    lineas: [],
    subtotal: '',
    iva: '',
    total: ''
});

function calc() {
    for (const linea of form.lineas) {
        const cantidad = parseFloat(linea.cantidad);
        const precioUnitario = parseFloat(linea.preciounit);
        if (!isNaN(cantidad) && !isNaN(precioUnitario)) {
            linea.total = (cantidad * precioUnitario).toFixed(2).toString();
        } else {
            linea.total = '';
        }
    }
    let subtotal = 0;
    for (const linea of form.lineas) {
        if (linea.total) {
            subtotal += parseFloat(linea.total);
        }
    }
    form.subtotal = subtotal.toFixed(2).toString();

    const iva = subtotal * 0.21;
    form.iva = iva.toFixed(2).toString();
    form.total = (subtotal + iva).toFixed(2).toString();
}


function addrow() {
    const nuevaLinea = {
        descripcion: '',
        cantidad: '',
        preciounit: '',
        total: ''
    };
    form.lineas.push(nuevaLinea);
}

function addcar() {

}

const generate = () => {
    form.post(route('generate'));
};
addrow()
</script>

<template>
    <Head title="Generador de Facturas" />
    <main class="container mt-2">
        <h1>Generador de Facturas</h1>
        <button class="btn btn-secondary me-2" type="button" @click="addrow()">Añadir fila</button>
        <button class="btn btn-secondary m-2" type="button" @click="addcar()">Añadir vehículo</button>

        <form @submit.prevent="generate">
            <div class="parent mt-2">
                <div class="row">
                    <div class="col-2 mt-2">
                        <InputLabel class="form-label" for="fecha">FECHA:</InputLabel>
                        <TextInput class="form-control" type="date" id="fecha" v-model="form.fecha" />
                    </div>
                    <div class="col mt-2">
                        <InputLabel class="form-label" for="titulo">TITULO:</InputLabel>
                        <TextInput class="form-control" type="text" id="titulo" v-model="form.titulo" />
                    </div>
                </div>
                <div v-for="(linea, index) in form.lineas" :key="index" class="row">
                    <div class="col mt-2">
                        <InputLabel class="form-label" :for="'descripcion-' + index">DESCRIPCIÓN:</InputLabel>
                        <TextInput class="form-control" type="text" :id="'descripcion-' + index"
                            v-model="linea.descripcion" />
                    </div>
                    <div class="col-2 mt-2">
                        <InputLabel class="form-label" :for="'cantidad-' + index">CANTIDAD:</InputLabel>
                        <TextInput class="form-control" type="number" :id="'cantidad-' + index" step="0.5" required
                            @change="calc()" v-model="linea.cantidad" />
                    </div>
                    <div class="col-2 mt-2">
                        <InputLabel class="form-label" :for="'preciounit-' + index">PRECIO UNIT.:</InputLabel>
                        <TextInput class="form-control" type="number" :id="'preciounit-' + index" required @change="calc()"
                            v-model="linea.preciounit" />
                    </div>
                    <div class="col-2 mt-2">
                        <InputLabel class="form-label" :for="'totalu-' + index">TOTAL:</InputLabel>
                        <TextInput class="form-control" type="number" :id="'totalu-' + index" aria-label="Read-only input"
                            readonly v-model="linea.total" />
                    </div>
                </div>
                <div class="row justify-content-end">
                    <div class="col-2 mt-2">
                        <InputLabel class="form-label" for="subtotal">SUBTOTAL</InputLabel>
                        <TextInput class="form-control" type="number" id="subtotal" aria-label="Read-only input" readonly
                            v-model="form.subtotal" />
                    </div>
                </div>
                <div class="row justify-content-end">
                    <div class="col-2 mt-2">
                        <InputLabel class="form-label" for="iva">IVA 21%</InputLabel>
                        <TextInput class="form-control" type="number" id="iva" aria-label="Read-only input" readonly
                            v-model="form.iva" />
                    </div>
                </div>
                <div class="row justify-content-end">
                    <div class="col-2 mt-2">
                        <InputLabel class="form-label" for="total"><b>TOTAL</b></InputLabel>
                        <TextInput class="form-control" type="number" id="total" aria-label="Read-only input" readonly
                            v-model="form.total" />
                    </div>
                </div>
            </div>
            <button class="btn btn-primary" type="submit">Generar Archivo</button>
        </form>
    </main>
</template>