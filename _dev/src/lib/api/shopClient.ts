import {HttpClientError} from './HttpClientError';

type Options = {
  shopUrl: string,
  onShopSessionLoggedOut?: Function,
};

const options: Options = {
  shopUrl: '',
};

export const initShopClient = (params: Options) => {
  options.shopUrl = params.shopUrl;
  options.onShopSessionLoggedOut = params.onShopSessionLoggedOut;
};

export const fetchShop = async (action: string, params?: { [key: string]: unknown }) => {
  if (!options.shopUrl.length) {
    throw new Error(`Cannot call action ${action}, API is not initialized (missing shop URL)`);
  }

  const response = await fetch(`${options.shopUrl}&action=${action}`, {
    method: 'POST',
    headers: {'Content-Type': 'application/json', Accept: 'application/json'},
    body: JSON.stringify({
      ajax: 1,
      action,
      ...params,
    }),
  });

  if (response.redirected && response.url.indexOf('AdminLogin') !== -1) {
    if (options.onShopSessionLoggedOut) {
      options.onShopSessionLoggedOut();
    }
    throw new HttpClientError('Unauthorized', 401);
  }

  if (!response.ok) {
    // TODO: Handle error messages returned by Facebook API
    // We would like to get the body, check if a reason is returned by FB
    // then return it properly.

    throw new HttpClientError(response.statusText, response.status);
  }

  return response.json();
};

export const getGenericRouteFromSpecificOne = (route: string): string => {
  const url = new URL(route);
  const genericSearchParams = new URLSearchParams();
  url.searchParams.forEach((value, param) => {
    if (['token', 'controller'].includes(param)) {
      genericSearchParams.set(param, value);
    }
  });
  url.search = `?${genericSearchParams.toString()}`;
  return url.toString();
};

export default {
  initShopClient,
  fetchShop,
};
