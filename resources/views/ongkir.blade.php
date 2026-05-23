<!DOCTYPE html>
<html>
<head>
    <title>Cek Ongkir</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

<form id="ongkirForm">
    <select id="province">
        <option value="">Pilih Provinsi</option>
    </select>

    <select id="city">
        <option value="">Pilih Kota</option>
    </select>

    <input type="number" id="weight" placeholder="Berat (gram)">

    <select id="courier">
        <option value="">Pilih Kurir</option>
        <option value="jne">JNE</option>
        <option value="tiki">TIKI</option>
        <option value="pos">POS</option>
    </select>

    <button type="submit">Cek Ongkir</button>
</form>

<div id="result"></div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    fetch('/provinces')
        .then(res => res.json())
        .then(data => {
            console.log(data);

            let provinceSelect = document.getElementById('province');

            data.data.forEach(province => {
                provinceSelect.innerHTML += `
                    <option value="${province.id}">
                        ${province.name}
                    </option>
                `;
            });
        });

    document.getElementById('province').addEventListener('change', function () {
        let provinceId = this.value;

        fetch(`/cities?province_id=${provinceId}`)
            .then(res => res.json())
            .then(data => {
                let citySelect = document.getElementById('city');
                citySelect.innerHTML = '<option value="">Pilih Kota</option>';

                data.data.forEach(city => {
                    citySelect.innerHTML += `
                        <option value="${city.id}">
                            ${city.name}
                        </option>
                    `;
                });
            });
    });

    document.getElementById('ongkirForm').addEventListener('submit', function(e) {
    e.preventDefault();

    let resultDiv = document.getElementById('result');
    resultDiv.innerHTML = 'Loading...';

    fetch('/cost', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            origin: 39,
            destination: document.getElementById('city').value,
            weight: document.getElementById('weight').value,
            courier: document.getElementById('courier').value
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log(data);

        resultDiv.innerHTML = '';

        if (!data.data) {
            resultDiv.innerHTML = 'Ongkir tidak ditemukan';
            return;
        }

        data.data.forEach(item => {
            resultDiv.innerHTML += `
                <div>
                    <b>${item.service}</b> - Rp ${item.cost.toLocaleString()} (${item.etd})
                </div>
            `;
        });
    })
    .catch(error => {
        console.error(error);
        resultDiv.innerHTML = 'Error backend / API';
    });
});

});
</script>

</body>
</html>
