export function priceWithoutTax(price, taxrate) {
  const withoutTax = price - (price * (taxrate / 100));
  return withoutTax.toFixed(2);
}

export function temp(){}
