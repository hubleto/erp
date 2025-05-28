import React, { Component } from 'react'
import { deepObjectMerge } from "adios/Helper";
import HubletoForm, {HubletoFormProps, HubletoFormState} from "../../../../src/core/Components/HubletoForm";
import Table, { TableProps, TableState } from 'adios/Table';

interface FormUserProps extends HubletoFormProps {
}

interface FormUserState extends HubletoFormState {
  showPremiumAccountWarning: boolean;
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

    this.state = {
      ...this.state,
      showPremiumAccountWarning: (this.state.id < 0)
    }
  }

  renderTitle(): JSX.Element {
    let title = ((this.state.record.first_name ?? '') + ' ' + (this.state.record.middle_name ?? '') + ' ' + (this.state.record.last_name ?? '')).trim();
    return <>
      <h2>{title == '' ? '-' : title}</h2>
      <small>User</small>
    </>;
  }

  renderContent(): JSX.Element {
    // const languages = {
    //   'cz': 'Česky',
    //   'de': 'Deutsch',
    //   'en': 'English',
    //   'es': 'Español',
    //   'fr': 'Francais',
    //   'pl': 'Polski',
    //   'sk': 'Slovensky',
    // };
    return <>
      <div className='w-full flex gap-2'>
        <div className="p-4 flex-1 text-center">
          <i className="fas fa-user text-primary" style={{fontSize: '8em'}}></i>

          {this.state.showPremiumAccountWarning ?
            <div className="block badge badge-warning p-4 mt-4 text-left">
              <i className="fas fa-triangle-exclamation mb-4 text-2xl"></i>
              <div>
                By adding or activating a new user, <b>your account may get automatically updated from Premium</b> and
                additional charges may be applied.<br/>
                <br/>
                <a href="https://www.hubleto.com/premium" target="_blank" className="btn btn-white btn-square">
                  <span className="icon"><i className="fas fa-arrow-up-right-from-square"></i></span>
                  <span className="text">Learn more about Premium accounts.</span>
                </a>
              </div>
            </div>
          : null}
        </div>
        <div className="flex-6">
          {this.divider('About the user')}
          {this.inputWrapper('first_name')}
          {this.inputWrapper('last_name')}
          {this.inputWrapper('nick')}
          {this.inputWrapper('email')}
          {this.inputWrapper('language')}
          {this.inputWrapper('id_default_company')}

          {this.divider('Access to Hubleto')}
          {this.inputWrapper('is_active', {
            readonly: this.state.id == globalThis.main.idUser,
            onChange: () => { this.setState({showPremiumAccountWarning: true} as FormUserState); },
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
      </div>
        {/* <div className="p-2">
          {Object.keys(languages).map((symbol) => {
            const lang = languages[symbol];

            return <>
              <a
                href={"?set-language=" + symbol}
                className={"btn btn-" + (symbol == this.state.record?.language ? "primary" : "transparent") + " mr-2"}
              ><span className="text">{lang}</span></a>
            </>;
          })}
        </div> */}
    </>;
  }
}
