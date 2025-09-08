import React, { Component, ChangeEvent } from "react";
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import InputTags2 from "@hubleto/react-ui/core/Inputs/Tags2";
import FormInput from "@hubleto/react-ui/core/FormInput";
import TableContacts from "../../Contacts/Components/TableContacts";
import { TabPanel, TabView } from "primereact/tabview";
import CustomerFormActivity, {CustomerFormActivityProps, CustomerFormActivityState} from "./CustomerFormActivity";
import ModalForm from "@hubleto/react-ui/core/ModalForm";
import { FormDealState } from "../../Deals/Components/FormDeal";
import TableDocuments from '@hubleto/apps/Documents/Components/TableDocuments';
import FormDocument, {FormDocumentProps, FormDocumentState} from "../../Documents/Components/FormDocument";
import FormContact, {FormContactProps, FormContactState} from "../../Contacts/Components/FormContact";
import Calendar from '../../Calendar/Components/Calendar'
import Hyperlink from "@hubleto/react-ui/core/Inputs/Hyperlink";
import request from "@hubleto/react-ui/core/Request";
import { FormProps, FormState } from "@hubleto/react-ui/core/Form";
import moment from "moment";
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
      {this.inputWrapper('description')}
      {this.inputWrapper('date')}
      {this.inputWrapper('reference')}

      { R.id > 0 &&
      <Table model="Hubleto/App/Community/Journal/Models/EntryLine" formProps={{
        model: 'Hubleto/App/Community/Journal/Models/EntryLine',
        description: { defaultValues: { id_entry: R.id }, inputs: { 'id_entry': { readonly: true } } }
      }} uid={this.props.uid} customEndpointParams={{ idEntry: R.id}}></Table>
      }
    </>;
  }

}
