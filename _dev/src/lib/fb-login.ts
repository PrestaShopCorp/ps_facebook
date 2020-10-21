import {v4} from 'uuid';

const openPopupGenerator = function (
  window: any,
  returnTo: string, // example, use window.location.href
  popupDomain: string, // example 'https://lui.ngrok.io'
  popupPath: string, // example '/index.html'
  shopName: string,
  ebid: string,
  jwt: string,
  currency: string, // example 'EUR'
  timezone: string, // example 'Europe/Paris'
  locale: string, // example 'en-US'
  correlationId: string,
  openCallback: Function,
  closeCallback: Function,
  responseCallback: Function,
  errorCallback?: Function,
) {
  let popup: any = null;
  let closingLooper: number | null = null;

  if (window.psFacebookOnbooardMessageListener) {
    window.removeEventListener('message', window.psFacebookOnbooardMessageListener);
  }
  // eslint-disable-next-line no-param-reassign
  window.psFacebookOnbooardMessageListener = window.addEventListener('message', (event: MessageEvent): boolean => {
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
  });

  console.log('openPopup() generated');
  return () => {
    const query = `?ebid=${ebid}&jwt=${jwt}&cur=${currency}&tz=${encodeURIComponent(timezone)
    }&locale=${encodeURIComponent(locale)}&corr=${encodeURIComponent(correlationId || v4())
    }&name=${encodeURIComponent(shopName)}&return_to=${encodeURIComponent(returnTo)}`;
    const p = 'scrollbars=no,resizable=no,status=no,location=no,toolbar=no,menubar=no,width=564,height=671';
    popup = window.open(popupDomain + popupPath + query, 'ps_facebook_fbe_onboarding', p);
    popup.focus();
    return popup;
  };
};

export default openPopupGenerator;
