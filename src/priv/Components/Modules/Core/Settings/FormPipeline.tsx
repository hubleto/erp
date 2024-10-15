import React, { Component } from 'react';
import { deepObjectMerge, getUrlParam } from 'adios/Helper';
import Form, { FormProps, FormState } from 'adios/Form';
import InputTags2 from 'adios/Inputs/Tags2';
import InputTable from 'adios/Inputs/Table';
import FormInput from 'adios/FormInput';
import TablePipelineSteps from './TablePipelineSteps';

interface FormPipelineProps extends FormProps {}

interface FormPipelineState extends FormState {}

export default class FormPipeline<P, S> extends Form<FormPipelineProps,FormPipelineState> {
  static defaultProps: any = {
    ...Form.defaultProps,
    model: 'CeremonyCrmApp/Modules/Core/Settings/Models/Pipeline',
  };

  props: FormPipelineProps;
  state: FormPipelineState;

  constructor(props: FormPipelineProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: FormPipelineProps) {
    return {
      ...super.getStateFromProps(props),
    };
  }

  /* normalizeRecord(record) {

    return record;
  } */

  renderHeaderLeft(): JSX.Element {
    return <>
      {this.state.isInlineEditing ? this.renderSaveButton() : this.renderEditButton()}
    </>;
  }

  renderHeaderRight(): JSX.Element {
    return <>
      {this.state.isInlineEditing ? this.renderDeleteButton() : null}
      {this.props.showInModal ? this.renderCloseButton() : null}
    </>;
  }

  renderTitle(): JSX.Element {
    if (getUrlParam('recordId') == -1) {
      return(
        <>
          <h2>
            {'New Pipeline'}
          </h2>
        </>
      );
    } else {
      return (
        <>
          <h2>
            {this.state.record.name
              ? this.state.record.name
              : '[Undefined Name]'}
          </h2>
        </>
      );
    }
  }

  /* onBeforeSaveRecord(record: any) {

    return record;
  } */

  renderContent(): JSX.Element {
    const R = this.state.record;
    const showAdditional = R.id > 0 ? true : false;

    return (
      <>
        <div className='grid grid-cols-2 gap-1' style=
          {{gridTemplateAreas:`
            'info info'
            'steps steps'
          `}}>
            <div className='card mt-4' style={{gridArea: 'info'}}>
              <div className='card-header'>Pipeline Information</div>
              <div className='card-body flex flex-row justify-around'>
                {this.inputWrapper('name')}
                {this.inputWrapper('description')}
              </div>
            </div>

            <div className='card mt-4' style={{gridArea: 'steps'}}>
              <div className='card-header'>Pipeline Steps</div>
              <div className='card-body'>
                <InputTable
                  uid={this.props.uid + '_table_pipeline_steps_input'}
                  {...this.getDefaultInputProps()}
                  value={R.PIPELINE_STEPS}
                  onChange={(value: any) => {
                    this.updateRecord({ PIPELINE_STEPS: value });
                  }}
                >
                  <TablePipelineSteps
                    uid={this.props.uid + '_pipeline_steps'}
                    context="Hello World"
                    descriptionSource="props"
                    description={{
                      ui: {
                        showFooter: false,
                        showHeader: false
                      },
                      permissions: {
                        canCreate: true,
                        canDelete: true,
                        canRead: true,
                        canUpdate: true
                      },
                      columns: {
                        name: { type: 'varchar', title: 'Name' },
                        order: { type: 'int', title: 'Order' },
                      }
                    }}
                  ></TablePipelineSteps>
                </InputTable>
                {this.state.isInlineEditing ? (
                  <a
                    role='button'
                    onClick={() => {
                      if (!R.PIPELINE_STEPS) R.PIPELINE_STEPS = [];
                      R.PIPELINE_STEPS.push({
                        id_pipeline: { _useMasterRecordId_: true },
                      });
                      this.setState({ record: R });
                    }}
                  >
                    + Add Pipeline Step
                  </a>
                ) : null}
              </div>
            </div>
        </div>
      </>
    );
  }
}
