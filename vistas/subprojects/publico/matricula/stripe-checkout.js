import STRIPE_KEYS from "../../../scripts/session/stripe-keys.js";
const fetchOptions = {
    headers: {
        Authorization: `Bearer ${STRIPE_KEYS.secret}`,
    }
}
let prices, products;
Promise.all([
    fetch("https://api.stripe.com/v1/products/prod_IVLplja4BX5V1a", fetchOptions),
    fetch("https://api.stripe.com/v1/prices", fetchOptions)
])
    .then(responses => Promise.all(responses.map((res) => res.json())))
    .then(json => {
        products = json[0];
        prices = json[1].data;
        //console.log(products, prices);
        prices.forEach(el => {
            if (el.product === products.id) {
                console.log(el);
                document.querySelector("#precioProduct").innerHTML = (el.unit_amount_decimal).slice(0, -2) + "." + (el.unit_amount_decimal).slice(-2) + " " + (el.currency).toLocaleUpperCase();
                document.querySelector("#comprarCurso").setAttribute("data-price", el.id);
            }

        });
    })
    .catch(err => {
        console.log(err);
    });
document.querySelector("#comprarCurso").addEventListener("click", (e) => {
    if (prices != undefined) {
        let price = e.target.getAttribute("data-price");
        Stripe(STRIPE_KEYS.public)
            .redirectToCheckout(
                {
                    lineItems: [{ price, quantity: 1 }],
                    mode: "payment",
                    successUrl: getHostFrontEnd() + "vistas/subprojects/modulo/stripe.html",
                    cancelUrl: getHostFrontEnd() + "vistas/subprojects/modulo/404.html",
                }
            )
            .then((res) => {
                console.log(res);
                if (res.error) {
                    console.log(error);
                }
            });
    }
});

