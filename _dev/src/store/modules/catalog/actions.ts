import {ActionContext} from 'vuex';
import {fetchShop} from '@/lib/api/shopClient';
import ActionsTypes from './actions-types';
import MutationsTypes from './mutations-types';
import {runIf} from '@/lib/Promise';
import {CategoryMatchingStatus, ProductFeedReport, State} from './state';
import {FullState} from '@/store';
import {PreScanCompleteResponseDto, PreScanIntermediateResponseDto, RequestState} from './types';

type Context = ActionContext<State, FullState>;

export default {
  async [ActionsTypes.WARMUP_STORE](
    {dispatch, state, getters}: Context,
  ) {
    if (state.warmedUp) {
      return;
    }
    state.warmedUp = true;

    await runIf(
      !getters.GET_CATALOG_PAGE_ENABLED,
      dispatch(ActionsTypes.REQUEST_SYNCHRONIZATION_STATS),
    );
  },

  async [ActionsTypes.REQUEST_SYNCHRONIZATION_STATS]({commit}: Context) {
    type SynchronizationStatusDto = {
      exportDone: boolean,
      exportOn: boolean,
      matchingDone: boolean,
      matchingProgress: {
        matched: number,
        total: number,
      },
      validation: {
        prevalidation: {
          syncable: number,
          notSyncable: number,
          lastScanDate: string,
        },
        reporting: {
          lastSyncDate: string,
          catalog: number,
          errored: number,
        }
      }
    };

    const result: SynchronizationStatusDto = await fetchShop('CatalogSummary');

    if (result.exportDone) {
      commit(MutationsTypes.SET_CATALOG_PAGE_ENABLED);
    }

    commit(MutationsTypes.SET_SYNCHRONIZATION_ACTIVE, result.exportOn);
    commit(MutationsTypes.SET_SYNCHRONIZATION_SUMMARY, {
      prevalidation: {
        ...result.validation.prevalidation,
        lastScanDate: new Date(result.validation.prevalidation.lastScanDate),
      },
      reporting: {
        ...result.validation.reporting,
        lastSyncDate: new Date(result.validation.reporting.lastSyncDate),
      },
    } as ProductFeedReport,
    );
    commit(MutationsTypes.SET_CATEGORY_MATCHING_SUMMARY, {
      matchingDone: result.matchingDone,
      matchingProgress: result.matchingProgress,
    } as CategoryMatchingStatus);
  },

  async [ActionsTypes.REQUEST_TOGGLE_SYNCHRONIZATION]({commit}: Context, newState: boolean) {
    type ToggleResultDto = {
      success: boolean,
      message?: string,
      turnOn: boolean,
    };

    commit(MutationsTypes.SET_REQUEST_STATE, {
      request: 'syncToggle',
      newState: RequestState.PENDING,
    });

    try {
      const result: ToggleResultDto = await fetchShop('requireProductSyncStart', {
        turn_on: newState,
      });

      if (!result.success) {
        throw new Error();
      }
      commit(MutationsTypes.SET_CATALOG_PAGE_ENABLED);
      commit(MutationsTypes.SET_SYNCHRONIZATION_ACTIVE, result.turnOn);
      commit(MutationsTypes.SET_REQUEST_STATE, {
        request: 'syncToggle',
        newState: RequestState.SUCCESS,
      });
    } catch {
      commit(MutationsTypes.SET_REQUEST_STATE, {
        request: 'syncToggle',
        newState: RequestState.FAILED,
      });
    }
  },

  async [ActionsTypes.REQUEST_PRODUCT_SCAN]({commit}: Context): Promise<number> {
    let page: number = 0;
    let inProgress: boolean = true;

    commit(MutationsTypes.SET_REQUEST_STATE, {
      request: 'scan',
      newState: RequestState.PENDING,
    });

    commit(MutationsTypes.SET_SYNCHRONIZATION_SUMMARY, {
      prevalidation: {
        lastScanDate: null,
        notSyncable: null,
        syncable: null,
      },
    } as Partial<ProductFeedReport>);

    try {
      for (page = 0; inProgress === true; page += 1) {
        /* eslint-disable no-await-in-loop */
        const result: PreScanIntermediateResponseDto|PreScanCompleteResponseDto = await fetchShop('RunPrevalidationScan', {
          page,
        });

        inProgress = !result.complete;

        if (result.complete) {
          commit(MutationsTypes.SET_SYNCHRONIZATION_SUMMARY, {
            prevalidation: {
              ...result.prevalidation,
              lastScanDate: new Date(result.prevalidation.lastScanDate),
            },
          } as Partial<ProductFeedReport>);
        }
      }

      commit(MutationsTypes.SET_REQUEST_STATE, {
        request: 'scan',
        newState: RequestState.SUCCESS,
      });
    } catch {
      commit(MutationsTypes.SET_REQUEST_STATE, {
        request: 'scan',
        newState: RequestState.FAILED,
      });
    }

    return page;
  },

  async [ActionsTypes.REQUEST_NEXT_SYNC_AS_FULL]({commit}: Context): Promise<void> {
    commit(MutationsTypes.SET_REQUEST_STATE, {
      request: 'requestNextSyncFull',
      newState: RequestState.PENDING,
    });

    try {
      await fetchShop('ExportWholeCatalog');

      commit(MutationsTypes.SET_REQUEST_STATE, {
        request: 'requestNextSyncFull',
        newState: RequestState.SUCCESS,
      });
    } catch {
      commit(MutationsTypes.SET_REQUEST_STATE, {
        request: 'requestNextSyncFull',
        newState: RequestState.FAILED,
      });
    }
  },
};
