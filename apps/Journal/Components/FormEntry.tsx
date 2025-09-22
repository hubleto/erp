import React, { Component, ChangeEvent } from "react";
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import Table from "@hubleto/react-ui/core/Table";

export interface FormEntryProps extends HubletoFormProps {
}

export interface FormEntryState extends HubletoFormState {
}

export default class FormEntry<P, S> extends HubletoForm<FormEntryProps, FormEntryState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: "Hubleto/App/Community/Journal/Models/Entry"
  };

  props: FormEntryProps;
  state: FormEntryState;

  refLogActivityInput: any;

  translationContext: string = 'Hubleto\\App\\Community\\Journal\\Loader::Components\\FormEntry';

  constructor(props: FormEntryProps) {
    super(props);

    this.refLogActivityInput = React.createRef();

    this.state = {
      ...this.getStateFromProps(props),
    }
  }

  renderContent(): JSX.Element {
    const R = this.state.record;
    // const showAdditional: boolean = R.id > 0 ? true : false;

    // const linkExists = this.state.description.defaultValues?.creatingForModel ? false : true

    return <>
      <div className="card">
        <div className="card-body">
          <div className="flex w-full">
            <div className="flex-grow">
              {this.inputWrapper('reference')}
            </div>
            {this.inputWrapper('date')}
          </div>
          {this.inputWrapper('description')}
        </div>
      </div>

      { R.id > 0 &&

        <div className="card mt-4">
          <div className="card-body">
            <Table model="Hubleto/App/Community/Journal/Models/EntryLine" formProps={{
              model: 'Hubleto/App/Community/Journal/Models/EntryLine',
              description: { defaultValues: { id_entry: R.id }, inputs: { 'id_entry': { readonly: true } } }
            }} uid={this.props.uid} customEndpointParams={{ idEntry: R.id}}></Table>
          </div>
        </div>
      }
    </>;
  }

}
