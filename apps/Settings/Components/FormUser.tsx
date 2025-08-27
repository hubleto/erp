import React, { Component } from 'react'
import { deepObjectMerge } from "@hubleto/react-ui/core/Helper";
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';
import request from '@hubleto/react-ui/core/Request';

interface FormUserProps extends HubletoFormProps { }
interface FormUserState extends HubletoFormState {
  appsInfo: any,
}

export default class FormUser<P, S> extends HubletoForm<FormUserProps, FormUserState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'HubletoApp/Community/Settings/Models/User',
  }

  props: FormUserProps;
  state: FormUserState;

  translationContext: string = 'HubletoApp\\Community\\Settings\\Loader::Components\\FormUser';

  constructor(props: FormUserProps) {
    super(props);
  }

  onAfterLoadFormDescription(description: any) {
    request.get(
      'api/get-apps-info',
      { },
      (appsInfo: any) => {
        this.setState({appsInfo: appsInfo});
      }
    );

    return description;
  }

  renderTitle(): JSX.Element {
    let title = ((this.state.record.first_name ?? '') + ' ' + (this.state.record.middle_name ?? '') + ' ' + (this.state.record.last_name ?? '')).trim();
    return <>
      <small>User</small>
      <h2>{title == '' ? '-' : title}</h2>
    </>;
  }

  renderContent(): JSX.Element {
    const R = this.state.record;

    let uApps = [];

    try {
      uApps = JSON.parse(R.apps);
    } catch (ex) {
      uApps = [];
    }

    if (!uApps) uApps = [];

    return <>
      <div className='w-full flex gap-2'>
        <div className="p-4 flex-1 text-center">
          <i className="fas fa-user text-primary" style={{fontSize: '8em'}}></i>
        </div>
        <div className="flex-6">
          {this.state.id == -1 && !globalThis.main.isPremium ?
            <div className="badge badge-warning text-lg w-full block p-8">
              You may add new users only in Premium account.<br/>
              <br/>
              <a href={globalThis.main.config.projectUrl + '/cloud'} className="btn btn-primary">
                <span className="icon"><i className="fas fa-medal"></i></span>
                <span className="text">Activate Premium account</span>
              </a>
            </div>
          : <>
            <div className='flex gap-2 flex-col md:flex-row'>
              <div className="flex-1">
                {this.divider(this.translate('About the user'))}
                {this.inputWrapper('type')}
                {this.inputWrapper('first_name')}
                {this.inputWrapper('last_name')}
                {this.inputWrapper('nick')}
                {this.inputWrapper('email')}
                {this.inputWrapper('language')}
                {this.inputWrapper('id_default_company')}

                {this.divider('Access to Hubleto')}
                {this.inputWrapper('is_active', {
                  readonly: this.state.id == globalThis.main.idUser,
                })}
                {this.inputWrapper('password')}

                {this.divider('Permissions')}

                {this.state.id < 0 ?
                  <div className="badge badge-info">First create user, then you will be prompted to assign roles.</div>
                :
                  <Table
                    uid='user_roles'
                    model='HubletoApp/Community/Settings/Models/UserHasRole'
                    customEndpointParams={{idUser: this.state.id}}
                  ></Table>
                }
              </div>
              <div className="flex-1 md:flex-row">
                {this.divider('Access to apps')}
                <div className="list">
                  {this.state.appsInfo ? <>
                    {Object.keys(this.state.appsInfo).map((appNamespace: any) => {
                      const app = this.state.appsInfo[appNamespace];
                      const permitted = app.permittedForAllUsers || uApps.includes(appNamespace);
                      return <button
                        key={appNamespace}
                        className={
                          "btn btn-small btn-list-item "
                          + (app.permittedForAllUsers ? "btn-disabled" : (permitted ? "btn-primary-outline" : "btn-transparent"))
                        }
                        onClick={() => {
                          if (!app.permittedForAllUsers) {
                            if (uApps.includes(appNamespace)) {
                              let uAppsNew = [];
                              for (let i in uApps) {
                                if (uApps[i] != appNamespace) uAppsNew.push(uApps[i]);
                              }
                              uApps = uAppsNew;
                            } else {
                              uApps.push(appNamespace);
                            }

                            this.updateRecord({apps: JSON.stringify(uApps)});
                          }
                        }}
                      >
                        <span className="icon"><i className={"fas fa-" + (permitted ? "square-check" : "square")}></i></span>
                        <span className="text">
                          {app.manifest.name}
                        </span>
                      </button>
                    })}
                  </> : null}
                </div>
              </div>
            </div>
          </>}
        </div>
      </div>
    </>;
  }
}
