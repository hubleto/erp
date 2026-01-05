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
    model: 'Hubleto/App/Community/Auth/Models/User',
  }

  props: FormUserProps;
  state: FormUserState;

  translationContext: string = 'Hubleto\\App\\Community\\Settings\\Loader';
  translationContextInner: string = 'Components\\FormUser';

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
      <small>{this.translate('User')}</small>
      <h2>{title == '' ? '-' : title}</h2>
    </>;
  }

  renderContent(): JSX.Element {
    const R = this.state.record;

    let permissions: any = {};

    try {
      permissions = JSON.parse(R.permissions);
    } catch (ex) {
      permissions = {};
    }

    if (!permissions) permissions = {};

    let uApps = [];

    try {
      uApps = JSON.parse(R.apps);
    } catch (ex) {
      uApps = [];
    }

    if (!uApps) uApps = [];

    return <>
      <div className='w-full flex gap-2'>
        {this.state.id == -1 && !globalThis.hubleto.isPremium ?
          <div className="badge badge-warning text-lg w-full block p-8">
            You may add new users only in Premium account.<br/>
            <br/>
            <a href={globalThis.hubleto.config.projectUrl + '/cloud'} className="btn btn-primary">
              <span className="icon"><i className="fas fa-medal"></i></span>
              <span className="text">{this.translate('Activate Premium account')}</span>
            </a>
          </div>
        : <>
          <div className="p-4 flex-1 text-center">
            <i className="fas fa-user text-primary" style={{fontSize: '8em'}}></i>
            <div className='mt-2'>
              {this.input('photo')}
            </div>
          </div>
          <div className="flex-6">
            <div className='flex gap-2 flex-col md:flex-row'>
              <div className="flex-1">
                {this.divider(this.translate('About the user'))}
                {this.inputWrapper('type')}
                {this.inputWrapper('first_name')}
                {this.inputWrapper('last_name')}
                {this.inputWrapper('nick')}
                {this.inputWrapper('position')}
                {this.inputWrapper('email')}
                {this.inputWrapper('phone_1')}
                {this.inputWrapper('phone_2')}
                {this.inputWrapper('language')}
                {this.inputWrapper('timezone')}
                {this.inputWrapper('id_default_company')}

                {this.divider('Access to Hubleto')}
                {this.inputWrapper('is_active', {
                  readonly: this.state.id == globalThis.hubleto.idUser,
                })}
                {this.inputWrapper('password')}

                {this.divider('Roles')}

                {this.state.id < 0 ?
                  <div className="badge badge-info">First create user, then you will be prompted to assign roles.</div>
                :
                  <Table
                    uid='user_roles'
                    model='Hubleto/App/Community/Auth/Models/UserHasRole'
                    customEndpointParams={{idUser: this.state.id}}
                  ></Table>
                }
              </div>
              <div className="flex-1 md:flex-row">
                {this.divider('Permissions for records with designated owner or manager')}
                <div className='list'>
                  <div className='list-item'><div className='flex gap-2 justify-between p-1'>
                    <div>Reading</div>
                    <div>
                      <select
                        onChange={(event) => {
                          console.log(event);
                          permissions.ownedRecordsRead = event.currentTarget.value;
                          console.log(permissions);
                          this.updateRecord({permissions: JSON.stringify(permissions)});
                        }}
                        value={permissions.ownedRecordsRead ?? 'owned'}
                      >
                        <option value='owned'>Can read only owned records</option>
                        <option value='owned-and-managed'>Can read only owned and managed records</option>
                        <option value='all'>Can read all records</option>
                      </select>
                    </div>
                  </div></div>
                  <div className='list-item'><div className='flex gap-2 justify-between p-1'>
                    <div>Modifying</div>
                    <div>
                      <select
                        onChange={(event) => {
                          console.log(event);
                          permissions.ownedRecordsModify = event.currentTarget.value;
                          this.updateRecord({permissions: JSON.stringify(permissions)});
                        }}
                        value={permissions.ownedRecordsModify ?? 'owned'}
                      >
                        <option value='owned'>Can modify only owned records</option>
                        <option value='owned-and-managed'>Can modify only owned and managed records</option>
                        <option value='all'>Can modify all records</option>
                      </select>
                    </div>
                  </div></div>
                </div>
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
          </div>
        </>}
      </div>
    </>;
  }
}
