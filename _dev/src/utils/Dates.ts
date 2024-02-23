export const toDateOrNull = (source: string|0): Date|null => {
  if (!source) {
    return null;
  }
  return new Date(source);
};

export default {
  toDateOrNull,
};
