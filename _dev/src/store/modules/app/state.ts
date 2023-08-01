export type State = {
  hooks: HooksStatuses;
}

export type HooksStatuses = {[key: string]: boolean};

export const state: State = {
  hooks: {},
};
