export function priceWithoutTax(price, taxrate) {
  const withoutTax = price - (price * (taxrate / 100));
  return withoutTax.toFixed(2);
}

export const asdf = '123';

export function getFullProductName(product) {
  let sufix = '';

  product.attributes.forEach((spec) => {
    sufix += ` ${spec.value}`;
  });
  return `${product.product_name} ${sufix}`;
}

export function extractOrderItemsMetaData(order) {
  return order.map(item => ({
    amount: item.amount,
    product_id: item.product.id,
  }));
}
