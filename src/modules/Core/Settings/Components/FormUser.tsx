import React, { Component } from 'react'
import { deepObjectMerge } from "adios/Helper";
import Form, { FormProps, FormState } from 'adios/Form';

interface FormUserProps extends FormProps {
}

interface FormUserState extends FormState {
}

export default class FormUser<P, S> extends Form<FormUserProps, FormUserState> {
  static defaultProps: any = {
    ...Form.defaultProps,
    model: 'CeremonyCrmMod/Core/Settings/Models/User',
  }

  props: FormUserProps;
  state: FormUserState;

  translationContext: string = 'mod.core.settings.formUser';

  constructor(props: FormUserProps) {
    super(props);
  }

  // getEndpointParams(): any {
  //   return {
  //     ...super.getEndpointParams(),
  //   }
  // }

  // loadParams() {
  //   let newState: any = deepObjectMerge({
  //     canCreate: true,
  //     canDelete: true,
  //     canRead: true,
  //     canUpdate: true,
  //     columns: {
  //       idCostingModel: {
  //         type: 'lookup', title: 'Costing model', 'model': 'AquilaCostingApp/Models/CostingModel',
  //         value: this.props.idCostingModel,
  //         readonly: true,
  //       },
  //       name: { type: 'varchar', title: 'Name' },
  //       isdcNumber: { type: 'varchar', title: 'ISDC Number' },
  //     },
  //   }, this.props);

  //   this.setState(newState, () => {
  //     this.loadRecord();
  //   });
  // }

  renderTitle(): JSX.Element {
    return <>
      <h2>{this.state.record.first_name ?? ''} {this.state.record.middle_name ?? ''} {this.state.record.last_name ?? ''}</h2>
      <small>My account</small>
    </>;
  }

  renderContent(): JSX.Element {
    const languages = {
      'cz': 'Česky',
      'de': 'Deutsch',
      'en': 'English',
      'es': 'Español',
      'fr': 'Francais',
      'pl': 'Polski',
      'sk': 'Slovensky',
    };
    return <>
      <div style={{
        display: 'flex',
        flexDirection: 'column',
        justifyContent: 'space-between',
        height: '100%',
      }}>
        <div>
          {this.inputWrapper('first_name')}
          {this.inputWrapper('middle_name')}
          {this.inputWrapper('last_name')}
          {this.inputWrapper('email')}
        </div>
        <div className="p-2">
          {Object.keys(languages).map((symbol) => {
            const lang = languages[symbol];

            return <>
              <a
                href={"?set-language=" + symbol}
                className={"btn btn-" + (symbol == this.state.record?.language ? "primary" : "transparent") + " mr-2"}
              ><span className="text">{lang}</span></a>
            </>;
          })}
        </div>
      </div>
    </>;
  }
}
