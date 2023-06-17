<script setup>
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const download = ref(false);

const form = useForm({
    cars: [],
    subtotal: '',
    iva: '',
    total: ''
});

function calc() {
    for (const vehiculo of form.cars) {
        for (const linea of vehiculo.lineas) {
            const cantidad = parseFloat(linea.cantidad);
            const precioUnitario = parseFloat(linea.preciounit);
            if (!isNaN(cantidad) && !isNaN(precioUnitario)) {
                linea.total = (cantidad * precioUnitario).toFixed(2);
            } else {
                linea.total = '';
            }
        }

        let subtotal = 0;
        for (const linea of vehiculo.lineas) {
            if (linea.total) {
                subtotal += parseFloat(linea.total) || 0;
            }
        }
        vehiculo.subtotal = subtotal.toFixed(2);
    }

    let totalGeneral = 0;
    for (const vehiculo of form.cars) {
        if (vehiculo.subtotal) {
            totalGeneral += parseFloat(vehiculo.subtotal) || 0;
        }
    }
    form.subtotal = totalGeneral.toFixed(2);

    const iva = totalGeneral * 0.21;
    form.iva = iva.toFixed(2);
    form.total = (totalGeneral + iva).toFixed(2);
}

function addrow(carIndex) {
    const newline = {
        descripcion: '',
        cantidad: '',
        preciounit: '',
        total: ''
    };
    if (form.cars[carIndex].lineas.length < 3) {
        form.cars[carIndex].lineas.push(newline);
        download.value = false;
    }
}

function addcar() {
    const newcar = {
        fecha: '',
        titulo: '',
        lineas: [{
            descripcion: '',
            cantidad: '',
            preciounit: '',
            total: ''
        }]
    };
    if (form.cars.length < 4) {
        form.cars.push(newcar);
        download.value = false;
    }
}

function showDownload() {
    download.value = true;
}

function submit() {
    form.post(route('generate'), {
        onSuccess: () => showDownload()
    })
}
addcar()
</script>

<template>
    <Head title="Generador de Facturas" />
    <main class="container mt-2 mb-5">
        <h1>Generador de Facturas</h1>
        <button class="btn btn-secondary" type="button" @click="addcar()">Añadir vehículo</button>

        <form @submit.prevent="submit">
            <div class="parent mt-2">
                <div v-for="(car, vIndex) in form.cars" :key="vIndex" class="mb-2">
                    <button class="btn btn-secondary mt-3" type="button" @click="addrow(vIndex)">Añadir fila</button>
                    <div class="row">
                        <div class="col-2 mt-2">
                            <InputLabel class="form-label" :for="'fecha-' + vIndex">FECHA:</InputLabel>
                            <TextInput class="form-control" type="date" :id="'fecha-' + vIndex" v-model="car.fecha" />
                        </div>
                        <div class="col mt-2">
                            <InputLabel class="form-label" :for="'titulo-' + vIndex">TITULO:</InputLabel>
                            <TextInput class="form-control" type="text" :id="'titulo-' + vIndex" v-model="car.titulo" required />
                        </div>
                    </div>
                    <div v-for="(linea, lIndex) in car.lineas" :key="lIndex" class="row mb-2">
                        <div class="col mt-2">
                            <InputLabel class="form-label" :for="'descripcion-'+ vIndex + lIndex">DESCRIPCIÓN:</InputLabel>
                            <TextInput class="form-control" type="text" :id="'descripcion-' + vIndex + lIndex" required
                                v-model="linea.descripcion" />
                        </div>
                        <div class="col-2 mt-2">
                            <InputLabel class="form-label" :for="'cantidad-' + vIndex + lIndex">CANTIDAD:</InputLabel>
                            <TextInput class="form-control" type="number" :id="'cantidad-'+ vIndex + lIndex" step="0.5" required
                                @change="calc()" v-model="linea.cantidad" />
                        </div>
                        <div class="col-2 mt-2">
                            <InputLabel class="form-label" :for="'preciounit-' + vIndex + lIndex">PRECIO UNIT.:</InputLabel>
                            <TextInput class="form-control" type="number" :id="'preciounit-'+ vIndex + lIndex" required
                                @change="calc()" v-model="linea.preciounit" />
                        </div>
                        <div class="col-2 mt-2">
                            <InputLabel class="form-label" :for="'totalu-' + vIndex + lIndex">TOTAL:</InputLabel>
                            <TextInput class="form-control" type="number" :id="'totalu-' + vIndex + lIndex"
                                aria-label="Read-only input" readonly v-model="linea.total" />
                        </div>
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
            <button class="btn btn-primary mb-2" type="submit">Generar Archivo</button>
        </form>
        <a v-if="download" class="btn btn-outline-success mb-2" href="/download">Descargar Factura</a>
    </main>
</template>