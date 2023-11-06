import {ActionContext} from 'vuex';
import {fetchShop} from '@/lib/api/shopClient';
import ActionsTypes from './actions-types';
import MutationsTypes from './mutations-types';
import {HooksStatuses, State} from './state';
import {FullState} from '@/store/types';

type Context = ActionContext<State, FullState>;

export default {
  async [ActionsTypes.GET_MODULES_VERSIONS]({commit}: Context, moduleName: string) {
    try {
      const result = await fetchShop('getModuleStatus', {moduleName});
      if (result.hooks) {
        commit(MutationsTypes.SET_HOOKS_STATUS, result.hooks);
      }
      return result;
    } catch (error) {
      console.error(error);
      return error;
    }
  },

  async [ActionsTypes.TRIGGER_MODULE_MANAGER_ACTION](
    {state}: Context,
    payload: {module: string, action: string},
  ): Promise<boolean> {
    const link = state.links.coreModuleActionUrl
      ?.replace('{module}', payload.module)
      .replace('{action}', payload.action);

    if (!link) {
      return false;
    }

    try {
      await fetch(link, {
        method: 'POST',
        headers: {'Content-Type': 'application/json', Accept: 'application/json'},
      });
    } catch {
      return false;
    }
    return true;
  },

  // eslint-disable-next-line no-empty-pattern
  async [ActionsTypes.TRIGGER_REGISTER_HOOK]({}: Context, hookName: string) {
    return fetchShop('registerHook', {hookName});
  },

  async [ActionsTypes.FIX_UNREGISTERED_HOOKS]({state, dispatch}): Promise<void> {
    if (!Object.entries(state.hooks).length) {
      await dispatch(ActionsTypes.GET_MODULES_VERSIONS, 'ps_facebook');
    }

    const hooksNames = Object.keys(state.hooks as HooksStatuses);

    for (let index = 0; index < hooksNames.length; index += 1) {
      const hookName = hooksNames[index];

      if (state.hooks[hookName] === false) {
        /* eslint-disable no-await-in-loop */
        await dispatch(ActionsTypes.TRIGGER_REGISTER_HOOK, hookName);
      }
    }

    await dispatch(ActionsTypes.GET_MODULES_VERSIONS, 'ps_facebook');
  },
};
