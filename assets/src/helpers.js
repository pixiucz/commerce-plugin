export function priceWithoutTax(price, taxrate) {
  const withoutTax = price - (price * (taxrate / 100));
  return withoutTax.toFixed(2);
}

export const asdf = '123';

export function getFullProductName(product) {
  let sufix = "";

  product.attributes.forEach(spec => {
    sufix += ` ${spec.value}`;
  });

  let prefix = "";
  
  if (product.brand_name) {
    prefix = product.brand_name;
  }

  return `${prefix} ${product.product_name} ${sufix}`;
}
