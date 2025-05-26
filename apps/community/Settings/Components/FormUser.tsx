import React, { Component } from 'react'
import { deepObjectMerge } from "adios/Helper";
import HubletoForm, {HubletoFormProps, HubletoFormState} from "../../../../src/core/Components/HubletoForm";
import Table, { TableProps, TableState } from 'adios/Table';

interface FormUserProps extends HubletoFormProps {
}

interface FormUserState extends HubletoFormState {
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
        <div className="p-4">
          <i className="fas fa-user text-primary" style={{fontSize: '8em'}}></i>
        </div>
        <div className="flex-1">
          {this.state.id < 0 ?
            <div className="badge badge-warning flex gap-2 items-center text-xl p-4">
              <i className="fas fa-triangle-exclamation"></i>
              <div>By adding a new user, your account will be updated from <u>personal</u> to <u>premium</u>. Additional charges may apply.</div>
            </div>
          : null}

          {this.divider('About the user')}
          {this.inputWrapper('first_name')}
          {this.inputWrapper('last_name')}
          {this.inputWrapper('nick')}
          {this.inputWrapper('email')}
          {this.inputWrapper('language')}
          {this.inputWrapper('id_default_company')}

          {this.divider('Access to Hubleto')}
          {this.inputWrapper('is_active')}
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
