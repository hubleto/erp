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

  translationContext: string = 'HubletoApp\\Community\\Calendar\\Loader::Components\\FormActivitySelector';

  render(): JSX.Element {
    var calendarConfigs = this.props.calendarConfigs;
    return (
      <>
        <div className='modal-header'>
          <div className="modal-header-left"></div>
          <div className="modal-header-title">{this.translate("New event")}</div>
          <div className="modal-header-right">
            <button className="btn btn-close" onClick={() => this.props.onCallback()}>
              <span className="text !py-2">&times;</span>
            </button>
          </div>
        </div>
        <div className="badge m-4 px-4 text-2xl">
          {this.props.clickConfig?.date}
          &nbsp;
          {this.props.clickConfig?.time}
        </div>
        <div className="badge badge-info m-4 px-4 text-xl">
          {this.translate("Choose calendar to which the event should be created.")}
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

                    this.setState({formSelected: globalThis.main.renderReactElement(calendarConfigs[item]["formComponent"],
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
                        showInModal: true,
                        showInModalSimple: true,
                        onClose:() => {this.setState({formSelected: null}), this.props.onCallback()},
                        onSaveCallback:() => {this.setState({formSelected: null}), this.props.onCallback()},
                      })
                    });
                  }}
                >
                  {item.icon ? <span className='icon'><i className={calendarConfigs[item]["icon"]}></i></span> : null}
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
            uid='activity_form'
            isOpen={true}
            type='inside-parent'
          >
            {this.state.formSelected}
          </ModalForm>
        : <></>}
      </>
    );
  }
}
