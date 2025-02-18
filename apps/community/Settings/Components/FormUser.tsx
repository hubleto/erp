import React, { Component } from 'react'
import { deepObjectMerge } from "adios/Helper";
import HubletoForm, {HubletoFormProps, HubletoFormState} from "../../../../src/core/Components/HubletoForm";

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

  translationContext: string = 'HubletoApp/Community/Settings/Components/FormUser';

  constructor(props: FormUserProps) {
    super(props);
  }

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
