
document.addEventListener("DOMContentLoaded", () => {

    document.querySelector("#btnSubmit").addEventListener("click", element => {

        element.preventDefault()

        let destinatario = document.querySelector("#destinatario").value
        let motivo = document.querySelector("#motivo").value
        let contenido = document.querySelector("#contenido").value

        sendMail(destinatario, motivo, contenido).then( resultado => {
            console.log(resultado)
        })
    })

})


// Función asíncrona para el envio de email
async function sendMail(destinatario, motivo, contenido){

    let form = new FormData()

    form.append("destinatario", destinatario)
    form.append("motivo", motivo)
    form.append("contenido", contenido)
    form.append("send", "mail")

    options = {method: 'POST',
                body: form}

    const response = await fetch("sendmail.php", options)
    const data = await response.json()

    return data
}