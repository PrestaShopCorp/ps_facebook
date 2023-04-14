import { cloneDeep } from "lodash";

export const contextPsAccountsNotConnected: Context = {
  currentContext: {
    type: 1,
    id: 1,
  },
  psxName: "psxmarketingwithgoogle",
  psIs17: true,
  accountsUiUrl: "https://accounts.distribution.prestashop.net/en",
  psAccountsIsInstalled: true,
  psAccountsInstallLink: null,
  psAccountsIsEnabled: true,
  psAccountsEnableLink: null,
  psAccountsIsUptodate: true,
  psAccountsUpdateLink: null,
  onboardingLink: "https://localhost",
  backendUser: {
    email: "admin@prestashop.com",
    employeeId: 1,
    isSuperAdmin: true,
  },
  user: {
    email: null,
    emailIsValidated: false,
    isSuperAdmin: true,
    uuid: null,
  },
  currentShop: {
    id: "1",
    name: "PrestaShop",
    domain: "placeholder.ngrok.io",
    domainSsl: "placeholder.ngrok.io",
    physicalUri: "/",
    publicKey:
      "-----BEGIN RSA PUBLIC KEY-----\r\nMIGJAoGBAL...1W2J5LLDnF/vdnLkqdMMfSmR+34OmDAgMBAAE=\r\n-----END RSA PUBLIC KEY-----",
    employeeId: 1,
    user: {
      email: null,
      emailIsValidated: false,
      isSuperAdmin: true,
    },
    isLinkedV4: false,
    multishop: false,
    moduleName: "psxmarketingwithgoogle",
    psVersion: "1.7.7.4",
    url: "https://placeholder.prestashop.com",
  },
  isShopContext: true,
  shops: [
    {
      id: "1",
      name: "Default",
      shops: [
        {
          id: "1",
          name: "PrestaShop",
          domain: "placeholder.ngrok.io",
          domainSsl: "placeholder.ngrok.io",
          physicalUri: "/",
          publicKey:
            "-----BEGIN RSA PUBLIC KEY-----\r\nMIGJAoGBAL...1W2J5LLDnF/vdnLkqdMMfSmR+34OmDAgMBAAE=\r\n-----END RSA PUBLIC KEY-----",
          url: "https://placeholder.ngrok.io/admin-dev/index.php?controller=AdminModules&configure=psxmarketingwithgoogle&setShopContext=s-1&token=ba2e131a1d891745a4e5389b890fc105",
          isLinkedV4: false,
          user: {
            email: null,
            emailIsValidated: false,
            isSuperAdmin: true,
            uuid: null,
          }
        },
      ],
      multishop: false,
      moduleName: "psxmarketingwithgoogle",
      psVersion: "1.7.7.2",
    },
  ],
  superAdminEmail: "some@email.com",
  ssoResendVerificationEmail:
    "https://auth.prestashop.com/account/send-verification-email",
  manageAccountLink: "https://auth.prestashop.com/login",
  adminAjaxLink: "https://localhost",
  dependencies: {
    ps_eventbus: {
      isInstalled: true,
      installLink: "https://localhost",
      isEnabled: true,
      enableLink: "https://localhost",
    },
  },
};

export const contextPsAccountsConnected = cloneDeep(contextPsAccountsNotConnected);
contextPsAccountsConnected.user = {
  email: "doge@thedog.com",
  emailIsValidated: false,
  isSuperAdmin: true,
};
// @ts-ignore
contextPsAccountsConnected.currentShop.uuid = 'fbbfgbkmmobgnjmeoLkSpQIdtULl1';
// @ts-ignore
contextPsAccountsConnected.currentShop.user = contextPsAccountsConnected.user;
// @ts-ignore
contextPsAccountsConnected.shops[0].shops[0] = contextPsAccountsConnected.currentShop;

export const contextPsAccountsConnectedAndValidated = cloneDeep(contextPsAccountsConnected);
// @ts-ignore
contextPsAccountsConnectedAndValidated.currentShop.user.emailIsValidated = true;


export default contextPsAccountsConnectedAndValidated;

/**
 * The folowing interfaces have been extracted from https://github.com/PrestaShopCorp/prestashop_accounts_vue_components/blob/796bb1bab2e1ea059b54bedd3f442e674467d098/src/types/context.ts
 */

export enum ShopContext {
  Shop = 1,
  Group = 2,
  All = 4,
}

export interface BackendUser {
  email?: string | null;
  employeeId?: number;
  isSuperAdmin: boolean;
}

export type Context = {
  accountsUiUrl?: string | null;
  adminAjaxLink?: string | null;
  backendUser?: Partial<BackendUser>;
  currentContext?: Partial<CurrentContext>;
  currentShop?: Shop | null;
  dependencies?: Record<
    string,
    {
      isInstalled?: boolean;
      isEnabled?: boolean;
      installLink?: string;
      enableLink?: string;
    }
  >;
  isOnboardedV4?: boolean;
  isShopContext?: boolean;
  manageAccountLink?: string | null;
  onboardingLink?: string | null;
  psAccountsEnableLink?: string | null;
  psAccountsInstallLink?: string | null;
  psAccountsIsEnabled?: boolean;
  psAccountsIsInstalled?: boolean;
  psAccountsIsUptodate?: boolean;
  psAccountsUpdateLink?: string | null;
  psAccountsVersion?: string;
  psIs17: boolean;
  psxName?: string;
  shops: ShopGroup[];
  ssoResendVerificationEmail?: string | null;
  superAdminEmail?: string | null;
  user?: Partial<User> | null;
  errors?: string[];
};

export interface CurrentContext {
  id?: number | null;
  type: ShopContext;
}

export interface Shop {
  domain: string | null;
  domainSsl: string | null;
  employeeId?: number | null; // string ?
  frontUrl?: string | null;
  id: string;
  isLinkedV4?: boolean; // ?
  moduleName?: string;
  multishop?: boolean;
  name: string;
  physicalUri?: string | boolean | null;
  psVersion?: string;
  publicKey: string;
  url: string;
  user?: Partial<User>;
  uuid?: string | null;
  virtualUri?: string | boolean | null;
}

export interface ShopGroup {
  id: string;
  moduleName?: string;
  multishop?: boolean;
  name: string;
  psVersion?: string;
  shops: Shop[];
}

export interface User {
  email?: string | null;
  emailIsValidated?: boolean;
  isSuperAdmin: boolean;
  uuid?: string | null;
}
