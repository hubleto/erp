import React, { Component } from 'react';
import HubletoForm, {HubletoFormProps, HubletoFormState} from "../../../../src/core/Components/HubletoForm";
import InputTags2 from 'adios/Inputs/Tags2';
import FormInput from 'adios/FormInput';
import { getUrlParam } from 'adios/Helper';

export interface FormDocumentProps extends HubletoFormProps {}

export interface FormDocumentState extends HubletoFormState {}

export default class FormDocument<P, S> extends HubletoForm<FormDocumentProps,FormDocumentState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'HubletoApp/Community/Documents/Models/Document',
  };

  props: FormDocumentProps;
  state: FormDocumentState;

  translationContext: string = 'HubletoApp\\Community\\Documents\\Loader::Components\\FormDocument';

  constructor(props: FormDocumentProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: FormDocumentProps) {
    return {
      ...super.getStateFromProps(props),
    };
  }

  renderTitle(): JSX.Element {
    if (getUrlParam('recordId') == -1) {
      return(
        <>
          <h2>
            {'New Document'}
          </h2>
        </>
      );
    } else {
      return (
        <>
          <h2>
            {this.state.record.name
              ?
              <div className='flex flex-col justify-center'>
                <span>{this.state.record.name}</span>
                <span className='text-xs text-gray-400 font-normal'>Document</span>
              </div>
              :
              <div className='flex flex-col justify-center'>
                <span>'[Undefined Name]'</span>
                <span className='text-xs text-gray-400 font-normal'>Document</span>
              </div>
              }
          </h2>
        </>
      );
    }
  }

  renderContent(): JSX.Element {
    const R = this.state.record;
    const showAdditional: boolean = R.id > 0 ? true : false;

    const linkExists = this.state.description.defaultValues?.creatingForModel ? false : true

    return (
      <>
        <div className='card mt-4'>
          <div className='card-body'>
              {this.inputWrapper('name', {readonly: this.props.readonly})}
              {this.inputWrapper('file', {readonly: this.props.readonly})}
              {this.inputWrapper('hyperlink', {readonly: this.props.readonly})}
              {R.origin_link && linkExists ?
                <a href={this.state.record.origin_link} className='btn brn-primary mt-2'>
                  <span className='icon'><i className='fas fa-link'></i></span>
                  <span className='text'>Go to origin entry</span>
                </a>
              : <></>
              }
          </div>
        </div>
      </>
    );
  }
}

