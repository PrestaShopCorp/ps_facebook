import {v4} from 'uuid';

const openPopupGenerator = (
  window: Window,
  returnTo: string, // example, use window.location.href
  popupDomain: string, // example 'https://lui.ngrok.io'
  popupPath: string, // example '/index.html'
  shopName: string,
  shopDomain: string,
  ebid: string,
  jwt: string,
  currency: string, // example 'EUR'
  timezone: string, // example 'Europe/Paris'
  locale: string, // example 'en-US'
  correlationId: string|null,
  openCallback: Function,
  closeCallback: Function,
  responseCallback: Function,
  errorCallback?: Function,
): () => Window|null => {
  let popup: Window|null = null;
  let closingLooper: number | null = null;

  const listener = (event: MessageEvent): boolean => {
    const strippedPopupDomain = popupDomain.replace(/^(https?:\/\/[^/]+)(.*)/, '$1');

    if (event.origin !== strippedPopupDomain) {
      console.log('Bad origin message. Ignored.', event, strippedPopupDomain);
      return true;
    }

    switch (event.data) {
      case 'READY':
        openCallback();
        if (closingLooper) {
          clearInterval(closingLooper);
        }
        closingLooper = setInterval(() => {
          if (popup && (popup.closed === true)) {
            if (closingLooper) clearInterval(closingLooper);
            closeCallback();
          }
        }, 750);
        break;
      default:
        if (event.data && event.data.fbe && event.data.fbe.error && errorCallback) {
          errorCallback(event.data.fbe.error);
        } // let responseCallback be called even if API did an error, to have partial data.
        responseCallback(event.data);
    }

    return true;
  };

  window.removeEventListener('message', listener);
  window.addEventListener('message', listener);

  console.log('openPopup() generated');
  return () => {
    const query = `?ebid=${ebid}&jwt=${jwt}&cur=${currency}&tz=${encodeURIComponent(timezone)
    }&locale=${encodeURIComponent(locale)}&corr=${encodeURIComponent(correlationId || v4())
    }&name=${encodeURIComponent(shopName)}&return_to=${encodeURIComponent(returnTo)
    }&shop_domain=${encodeURIComponent(shopDomain)}`;
    const p = 'scrollbars=no,resizable=no,status=no,location=no,toolbar=no,menubar=no,width=564,height=671';
    popup = window.open(popupDomain + popupPath + query, 'ps_facebook_fbe_onboarding', p);
    if (popup) {
      popup.focus();
    }
    return popup;
  };
};

export default openPopupGenerator;
