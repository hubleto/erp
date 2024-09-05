import React, { Component } from "react";
import { deepObjectMerge } from "adios/Helper";
import Form, { FormProps, FormState } from "adios/Form";
import InputVarchar from "adios/Inputs/Varchar";
import InputTags2 from "adios/Inputs/Tags2";
import InputTable from "adios/Inputs/Table";
import FormInput from "adios/FormInput";
import { Column } from "primereact/column";

interface FormActivityProps extends FormProps {}

interface FormActivityState extends FormState {}

export default class FormActivity<P, S> extends Form<FormActivityProps,FormActivityState> {
  static defaultProps: any = {
    model: "CeremonyCrmApp/Modules/Core/Customers/Models/Person",
  };

  props: FormActivityProps;
  state: FormActivityState;

  constructor(props: FormActivityProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: FormActivityProps) {
    return {
      ...super.getStateFromProps(props),
    };
  }

  normalizeRecord(record) {
    if (record.TAGS) record.TAGS.map((item: any, key: number) => {
      record.TAGS[key].id_activity = {_useMasterRecordId_: true};
    });

    return record;
  }

  renderTitle(): JSX.Element {
    return (
      <>
        <h2>
          {this.state.record.last_name
            ? this.state.record.subject
            : "[no-name]"}
        </h2>
      </>
    );
  }

  renderContent(): JSX.Element {
    const R = this.state.record;

    return (
      <>
        <div className="grid grid-cols-2 gap-1">
          <div>
            <div className="card mt-4">
              <div className="card-header">Activity Information</div>
              <div className="card-body">
                {this.inputWrapper("subject")}
                {this.inputWrapper("id_company")}
                {this.inputWrapper("due_date")}
                {this.inputWrapper("due_time")}
                {this.inputWrapper("duration")}
                {this.inputWrapper("completed")}

                <FormInput title='Categories'>
                  <InputTags2 {...this.getDefaultInputProps()}
                    value={this.state.record.TAGS}
                    model='CeremonyCrmApp/Modules/Core/Customers/Models/Tag'
                    targetColumn='id_activity'
                    sourceColumn='id_tag'
                    colorColumn='color'
                    onChange={(value: any) => {
                      this.updateRecord({TAGS: value});
                    }}
                  ></InputTags2>
                </FormInput>
              </div>
            </div>
          </div>

          <div>
            <div className="card">
              <div className="card-header">this.state.record</div>
              <div className="card-body">
                <pre
                  style={{
                    color: "blue",
                    width: "100%",
                    fontFamily: "Courier New",
                    fontSize: "10px",
                  }}
                >
                  {JSON.stringify(R, null, 2)}
                </pre>
              </div>
            </div>
          </div>
        </div>
      </>
    );
  }
}
