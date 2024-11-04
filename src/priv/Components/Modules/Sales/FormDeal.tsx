import React, { Component } from 'react';
import { deepObjectMerge, getUrlParam } from 'adios/Helper';
import Form, { FormProps, FormState } from 'adios/Form';
import InputTags2 from 'adios/Inputs/Tags2';
import InputTable from 'adios/Inputs/Table';
import FormInput from 'adios/FormInput';
import request from 'adios/Request';

interface FormDealProps extends FormProps {}

interface FormDealState extends FormState {}

export default class FormDeal<P, S> extends Form<FormDealProps,FormDealState> {
  static defaultProps: any = {
    ...Form.defaultProps,
    model: 'CeremonyCrmApp/Modules/Sales/Sales/Models/Deal',
  };

  props: FormDealProps;
  state: FormDealState;

  constructor(props: FormDealProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: FormDealProps) {
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
            {'New Deal'}
          </h2>
        </>
      );
    } else {
      return (
        <>
          <h2>
            {this.state.record.title
              ? this.state.record.title
              : '[Undefined Deal Name]'}
          </h2>
        </>
      );
    }
  }

  changeDealStatus(idStep: number, R: any) {
    if (idStep == R.id_pipeline_step) return;
    request.get(
      'sales/change-pipeline-step',
      {
        idStep: idStep,
        idPipeline: R.id_pipeline,
        idDeal: R.id
      },
      (data: any) => {
        if (data.status == "success") {
          R.id_pipeline_step = data.returnStep.id;
          R.HISTORY = data.dealHistory;
          this.setState({record: R});
        }
      }
    );
  }

  renderContent(): JSX.Element {
    const R = this.state.record;
    const showAdditional = R.id > 0 ? true : false;

    return (
      <>
        <div className='grid grid-cols-2 gap-1' style=
          {{gridTemplateAreas:`
            'info info'
            'status status'
            'history history'
          `}}>
            <div className='card mt-2' style={{gridArea: 'info'}}>
              <div className='card-header'>Deal Information</div>
              <div className='card-body flex flex-row gap-2'>
                <div className='grow'>
                  {this.inputWrapper('title')}
                  {this.inputWrapper('id_company')}
                  {this.inputWrapper('id_person')}
                  <div className='flex flex-row *:w-1/2'>
                    {this.inputWrapper('price')}
                    {this.inputWrapper('id_currency')}
                  </div>
                  {/* {showAdditional ? this.inputWrapper('id_status') : null} */}
                  {showAdditional && R.id_lead != null ?
                    <div className='flex flex-row justify-between'>
                      {this.inputWrapper('id_lead')}
                      <a className='btn btn-primary self-center' href={`leads?recordId=${R.id_lead}`}>
                        <span className='icon'><i className='fas fa-eye'></i></span>
                      </a>
                    </div>
                  : null}
                </div>
                <div className='border-l border-gray-200'></div>
                <div className='grow'>
                  {this.inputWrapper('id_user')}
                  {this.inputWrapper('date_expected_close')}
                  {this.inputWrapper('source_channel')}
                  {this.inputWrapper('note')}
                  <FormInput title='Labels'>
                    <InputTags2 {...this.getDefaultInputProps()}
                      value={this.state.record.LABELS}
                      model='CeremonyCrmApp/Modules/Core/Settings/Models/Label'
                      targetColumn='id_deal'
                      sourceColumn='id_label'
                      colorColumn='color'
                      onChange={(value: any) => {
                        this.updateRecord({LABELS: value});
                      }}
                    ></InputTags2>
                  </FormInput>
                  {showAdditional ? this.inputWrapper('is_archived') : null}
                </div>
              </div>
            </div>
            {showAdditional ?
              <>
                <div className='card mt-2' style={{gridArea: 'status'}}>
                  <div className='card-header'>Deal Progress</div>
                  <div className='card-body'>
                    {this.inputWrapper('id_pipeline')}
                    <div className=' flex flex-row gap-2 justify-center'>

                      {R.PIPELINE != null && R.PIPELINE.PIPELINE_STEPS.length > 0 ?
                        R.PIPELINE.PIPELINE_STEPS.map((s, i) => {
                          var statusColor: string = null;
                          {s.id <= R.id_pipeline_step ? statusColor = "btn-primary" : statusColor = "btn-light"}
                          return (
                            <>
                              <button
                                style={{height: "50px"}}
                                onClick={()=>{this.changeDealStatus(s.id, R)}}
                                className={`flex px-3 justify-center btn ${statusColor}`}
                              >
                                <span className='text text-center self-center'>{s.name}</span>
                              </button>
                              {i+1 == R.PIPELINE.PIPELINE_STEPS.length ? null
                              : <span className='icon flex'><i className='fas fa-angles-right self-center'></i></span>
                              }
                            </>
                          )
                        })
                      : null}

                    </div>
                  </div>
                </div>
                <div className='card mt-2' style={{gridArea: 'history'}}>
                  <div className='card-header'>Deal History</div>
                  <div className='card-body min-h-[100px] flex justify-center' style={{flexDirection: "column", gap: "4px"}}>
                    {R.HISTORY.length > 0 ?
                      R.HISTORY.map((history, key) => (
                        <div className='w-full flex flex-row justify-between'>
                          <div className='w-1/3'>
                              <p className='font-bold self-center text-sm text-left'>
                                {history.description}
                              </p>
                            </div>
                          <div className='w-1/3' style={{alignContent: "center"}}>
                            <hr style={{width: "100%", alignSelf: "center"}}/>
                          </div>
                          <div className='w-1/3 justify-center'>
                            <p className='self-center text-sm text-center'>
                              {history.change_date}
                            </p>
                          </div>
                        </div>
                      ))
                      :
                      <p className='text-gray-400'>Deal has no history</p>
                    }
                  </div>
                </div>
              </>

            : null}
        </div>
      </>
    );
  }
}