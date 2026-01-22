import ModalForm from '@hubleto/react-ui/core/ModalForm';
import TranslatedComponent from '@hubleto/react-ui/core/TranslatedComponent';
import moment from 'moment';
import React, { Component } from 'react';

export interface FormActivitySelectorProps {
  calendarConfigs: any,
  clickConfig: any,
  onCallback: Function,
}

export interface FormActivitySelectorState {
  formSelected?: JSX.Element;
}

export default class FormActivitySelector<P, S> extends TranslatedComponent<FormActivitySelectorProps, FormActivitySelectorState>{

  props: FormActivitySelectorProps;
  state: FormActivitySelectorState;

  translationContext: string = 'Hubleto\\App\\Community\\Calendar\\Loader';
  translationContextInner: string = 'Components\\FormActivitySelector';

  refActivityModal: any;

  constructor(props: FormActivitySelectorProps) {
    super(props);

    this.refActivityModal = React.createRef();
  }

  render(): JSX.Element {
    var calendarConfigs = this.props.calendarConfigs;
    return (
      <>
        <div className='modal-header active'>
          <div className="modal-header-left">
            <button className="btn btn-add" onClick={() => this.props.onCallback()}>
              <span className="icon">
                <i className="fas fa-save"></i>
              </span>
              <span className="text">Save</span>
            </button>
          </div>
          <div className="modal-header-title">
            <h2>{this.translate("New event")}</h2>
          </div>
          <div className="modal-header-right">
            <button
              className="btn btn-close"
              type="button"
              aria-label="Close"
              onClick={() => this.props.onCallback()}>
              <span className="icon">
                <i className="fas fa-xmark"></i>
                <span className="shortcut">Esc</span>
              </span>
            </button>
          </div>
        </div>
        <div className="badge m-4 px-4 text-2xl">
          {this.props.clickConfig?.date}
          &nbsp;
          {this.props.clickConfig?.time}
        </div>
        <div className="badge badge-info m-4 px-4 text-xl">
            {this.translate("Choose the calendar where you want to create the event.")}
        </div>
        <div className='flex gap-2 flex-col px-4 mt-4'>
          {Object.keys(this.props.calendarConfigs).map((item, index) => {
            if (calendarConfigs[item]["title"]) {
              return <>
                <button
                  key={index}
                  className='btn btn-transparent btn-large'
                  style={{borderLeft: '3em solid ' + calendarConfigs[item]["color"]}}
                  onClick={() => {
                    //calculate half an hour from time_start
                    if (this.props.clickConfig?.time && this.props.clickConfig?.date) {
                      var momentDateTime = moment(`${this.props.clickConfig?.date} ${this.props.clickConfig?.time}`, "YYYY-MM-DD HH:mm:ss");
                      var newMoment = momentDateTime.add(30, 'minutes');
                    }

                    this.setState({formSelected: globalThis.hubleto.renderReactElement(calendarConfigs[item]["formComponent"],
                      {
                        description: {
                          defaultValues: {
                            date_start: this.props.clickConfig?.date,
                            time_start: this.props.clickConfig?.time,
                            date_end: this.props.clickConfig?.date,
                            time_end: newMoment?.format("HH:mm:ss"),
                          }
                        },
                        id: -1,
                        modal: this.refActivityModal,
                        onClose:() => {this.setState({formSelected: null})},
                        onSaveCallback:() => {this.setState({formSelected: null}), this.props.onCallback()},
                      })
                    });
                  }}
                >
                  <span className='text text-center self-center !h-auto text-lg'>{calendarConfigs[item]["title"]}</span>
                </button>
              </>
            } else {
              return null;
            }
          })}
        </div>
        {this.state?.formSelected ?
          <ModalForm
            ref={this.refActivityModal}
            uid='activity_form'
            isOpen={true}
            type='inside-parent'
            onClose={() => this.setState({formSelected: null})}
          >
            {this.state.formSelected}
          </ModalForm>
        : <></>}
      </>
    );
  }
}
