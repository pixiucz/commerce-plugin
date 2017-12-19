// Transform object properties to query parameters
export function transformObjectToQueryParams(obj) {
  const str = [];
  Object.keys(obj).forEach((p) => {
    if (Object.prototype.hasOwnProperty.call(obj, p)) {
      str.push(`${encodeURIComponent(p)}=${encodeURIComponent(obj[p])}`);
    }
  });
  return str.join('&');
}

export function priceWithoutTax(price, taxrate) {
  return price - (price * (taxrate / 100));
}
