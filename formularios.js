// Máscaras: a pontuação não conta como número do telefone ou do CEP.
const campoCep = document.getElementById('cep');
const campoTelefone = document.getElementById('telefone');
const campoCarga = document.getElementById('carga_horaria');

function formatarCep(valor) {
    const numeros = valor.replace(/\D/g, '').slice(0, 8);
    return numeros.length > 5 ? numeros.slice(0, 5) + '-' + numeros.slice(5) : numeros;
}

function formatarTelefone(valor) {
    const numeros = valor.replace(/\D/g, '').slice(0, 11);
    if (numeros.length <= 2) return numeros;
    const ddd = numeros.slice(0, 2);
    const telefone = numeros.slice(2);
    if (telefone.length <= 4) return '(' + ddd + ') ' + telefone;
    const separacao = telefone.length > 8 ? 5 : 4;
    return '(' + ddd + ') ' + telefone.slice(0, separacao) + '-' + telefone.slice(separacao);
}

if (campoCep) {
    campoCep.addEventListener('input', () => { campoCep.value = formatarCep(campoCep.value); });
}
if (campoTelefone) {
    campoTelefone.addEventListener('input', () => { campoTelefone.value = formatarTelefone(campoTelefone.value); });
}
if (campoCarga) {
    campoCarga.addEventListener('input', () => {
        campoCarga.value = campoCarga.value.replace(/\D/g, '').slice(0, 5);
        const valor = Number(campoCarga.value);
        campoCarga.setCustomValidity(campoCarga.value && (valor < 1 || valor > 20000)
            ? 'Informe uma carga horária de 1 a 20000 horas.' : '');
    });
}
